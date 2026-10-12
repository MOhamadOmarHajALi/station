<?php

namespace App\Console\Commands;

use App\Models\RecurringInvoice;
use App\Services\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunRecurringInvoices extends Command
{
    protected $signature = 'station:run-recurring';
    protected $description = 'إنشاء الفواتير المتكررة المستحقة هذا الشهر تلقائيًا';

    public function handle(InvoiceService $service): int
    {
        $today = now();
        $created = 0;
        $failed = 0;

        foreach (RecurringInvoice::where('active', true)->get() as $r) {
            $dueDay = min($r->day_of_month, $today->daysInMonth);
            $alreadyRan = $r->last_run_on && $r->last_run_on->format('Y-m') === $today->format('Y-m');

            if ($alreadyRan || $today->day < $dueDay) {
                continue; // لم يحن موعدها أو أُنشئت هذا الشهر
            }

            try {
                $service->create([
                    'type' => $r->type,
                    'invoice_date' => $today->toDateString(),
                    'customer_id' => $r->customer_id,
                    'photographer_id' => $r->photographer_id,
                    'title' => $r->title,
                    'notes' => 'فاتورة متكررة (تلقائية)',
                ], [['description' => $r->title, 'quantity' => 1, 'price' => $r->amount]]);

                $r->update(['last_run_on' => $today->toDateString()]);
                $created++;
            } catch (Throwable $e) {
                $failed++;
                report($e);
                Log::error('Recurring invoice failed', ['id' => $r->id, 'error' => $e->getMessage()]);
            }
        }

        $this->info("تم إنشاء {$created} فاتورة".($failed ? " — وفشلت {$failed} (راجع السجل)" : ''));
        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
