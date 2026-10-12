<?php

namespace App\Http\Controllers;

use App\Models\{Customer, Invoice, Photographer};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $r)
    {
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $r->query('month')) ? $r->query('month') : now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $sum = fn (string $type, Carbon $a, Carbon $b) =>
            (float) Invoice::ofType($type)->betweenDates($a->toDateString(), $b->toDateString())->sum('total');

        $income = $sum('income', $start, $end);
        $expense = $sum('expense', $start, $end);
        $profit = $income - $expense;

        $pStart = $start->copy()->subMonthNoOverflow()->startOfMonth();
        $pEnd = $pStart->copy()->endOfMonth();
        $pIncome = $sum('income', $pStart, $pEnd);
        $pExpense = $sum('expense', $pStart, $pEnd);

        $delta = fn ($now, $prev) => $prev == 0 ? null : (int) round(($now - $prev) / abs($prev) * 100);
        $deltas = [
            'income' => $delta($income, $pIncome),
            'expense' => $delta($expense, $pExpense),
            'profit' => $delta($profit, $pIncome - $pExpense),
        ];

        $chart = ['labels' => [], 'income' => [], 'expense' => []];
        for ($i = 5; $i >= 0; $i--) {
            $s = $start->copy()->subMonthsNoOverflow($i)->startOfMonth();
            $e = $s->copy()->endOfMonth();
            $chart['labels'][] = $s->translatedFormat('F');
            $chart['income'][] = $sum('income', $s, $e);
            $chart['expense'][] = $sum('expense', $s, $e);
        }

        $partners = collect(config('station.partners'))->map(fn ($p) => $p + [
            'amount' => $profit > 0 ? round($profit * $p['percent'] / 100) : 0,
        ])->values();

        $range = [$start->toDateString(), $end->toDateString()];

        $topCustomers = Customer::withSum(['invoices as month_total' => fn ($q) => $q->ofType('income')->betweenDates(...$range)], 'total')
            ->orderByDesc('month_total')->limit(5)->get()
            ->filter(fn ($c) => $c->month_total > 0)->map(fn ($c) => ['name' => $c->name, 'total' => (float) $c->month_total])->values();

        $photographerCosts = Photographer::withSum(['invoices as month_total' => fn ($q) => $q->ofType('expense')->betweenDates(...$range)], 'total')
            ->orderByDesc('month_total')->limit(5)->get()
            ->filter(fn ($p) => $p->month_total > 0)->map(fn ($p) => ['name' => $p->name, 'total' => (float) $p->month_total])->values();

        $recent = Invoice::with(['customer', 'photographer'])->latest('invoice_date')->latest('id')->limit(8)->get()
            ->map(fn ($i) => [
                'id' => $i->id, 'number' => $i->number, 'type' => $i->type,
                'party' => $i->type === 'income' ? $i->customer?->name : ($i->photographer?->name ?? $i->title),
                'date' => $i->invoice_date->format('Y-m-d'), 'total' => (float) $i->total,
            ]);

        return Inertia::render('Dashboard', [
            'month' => $month,
            'monthLabel' => $start->translatedFormat('F Y'),
            'income' => $income, 'expense' => $expense, 'profit' => $profit,
            'deltas' => $deltas, 'chart' => $chart, 'partners' => $partners,
            'topCustomers' => $topCustomers, 'photographerCosts' => $photographerCosts, 'recent' => $recent,
            'counts' => [
                'customers' => Customer::count(), 'photographers' => Photographer::count(),
                'invoices' => Invoice::betweenDates(...$range)->count(),
            ],
        ]);
    }
}
