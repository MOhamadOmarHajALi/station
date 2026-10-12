<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function monthly(Request $r)
    {
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $r->query('month')) ? $r->query('month') : now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();
        $range = [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()];

        $map = fn ($i) => [
            'id' => $i->id, 'number' => $i->number, 'date' => $i->invoice_date->format('m-d'),
            'party' => $i->type === 'income' ? $i->customer?->name : ($i->photographer?->name ?? $i->title),
            'total' => (float) $i->total,
        ];

        $incomeInvoices = Invoice::with('customer:id,name')->ofType('income')->betweenDates(...$range)->orderBy('invoice_date')->get();
        $expenseInvoices = Invoice::with('photographer:id,name')->ofType('expense')->betweenDates(...$range)->orderBy('invoice_date')->get();

        $income = (float) $incomeInvoices->sum('total');
        $expense = (float) $expenseInvoices->sum('total');
        $profit = $income - $expense;

        return Inertia::render('Reports/Monthly', [
            'month' => $month, 'monthLabel' => $start->translatedFormat('F Y'),
            'income' => $income, 'expense' => $expense, 'profit' => $profit,
            'partners' => collect(config('station.partners'))->map(fn ($p) => $p + [
                'amount' => $profit > 0 ? round($profit * $p['percent'] / 100, 2) : 0,
            ])->values(),
            'incomeInvoices' => $incomeInvoices->map($map)->values(),
            'expenseInvoices' => $expenseInvoices->map($map)->values(),
        ]);
    }
}
