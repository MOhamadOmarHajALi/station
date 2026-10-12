<?php

namespace App\Console\Commands;

use App\Models\{Invoice, User};
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Log, Mail};
use Throwable;

class SendMonthlyReport extends Command
{
    protected $signature = 'station:monthly-report {--month= : YYYY-MM (الافتراضي الشهر الماضي)}';
    protected $description = 'إرسال ملخص الشهر (واردات/صادرات/أرباح وتوزيعها) إلى المدراء بالبريد';

    public function handle(): int
    {
        $start = $this->option('month')
            ? Carbon::createFromFormat('Y-m-d', $this->option('month').'-01')->startOfMonth()
            : now()->subMonthNoOverflow()->startOfMonth();
        $range = [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()];

        $income = (float) Invoice::ofType('income')->betweenDates(...$range)->sum('total');
        $expense = (float) Invoice::ofType('expense')->betweenDates(...$range)->sum('total');
        $profit = $income - $expense;
        $cur = config('station.currency');

        $lines = ['تقرير Station — '.$start->translatedFormat('F Y'), '',
            'الواردات: '.number_format($income, 2)." $cur",
            'الصادرات: '.number_format($expense, 2)." $cur",
            'صافي الربح: '.number_format($profit, 2)." $cur", '', 'توزيع الأرباح:'];
        foreach (config('station.partners') as $p) {
            $amt = $profit > 0 ? $profit * $p['percent'] / 100 : 0;
            $lines[] = "- {$p['name']} ({$p['percent']}%): ".number_format($amt, 2)." $cur";
        }

        $emails = User::where('role', 'admin')->pluck('email')->all();
        if (! $emails) {
            $this->warn('لا يوجد مدراء لإرسال التقرير إليهم.');
            return self::SUCCESS;
        }

        try {
            Mail::raw(implode("\n", $lines), fn ($m) => $m->to($emails)->subject('Station — تقرير '.$start->format('Y-m')));
            $this->info('تم إرسال التقرير إلى: '.implode(', ', $emails));
            return self::SUCCESS;
        } catch (Throwable $e) {
            report($e);
            Log::error('Monthly report mail failed', ['error' => $e->getMessage()]);
            $this->error('فشل الإرسال: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
