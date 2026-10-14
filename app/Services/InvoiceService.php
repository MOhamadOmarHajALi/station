<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /** ينشئ فاتورة مع بنودها ويحسب الإجمالي داخل transaction واحدة */
    public function create(array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($data, $items) {
            $invoice = Invoice::create($data + ['number' => Invoice::nextNumber($data['type']), 'total' => 0]);
            $this->syncItems($invoice, $items);
            return $invoice;
        });
    }

    /** يعدّل بيانات الفاتورة ويستبدل بنودها ويعيد حساب الإجمالي (الرقم والنوع لا يتغيران) */
    public function update(Invoice $invoice, array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($invoice, $data, $items) {
            $invoice->update($data);
            $invoice->items()->delete();
            $this->syncItems($invoice, $items);
            return $invoice;
        });
    }

    private function syncItems(Invoice $invoice, array $items): void
    {
        $total = 0;
        foreach ($items as $item) {
            $sub = $item['quantity'] * $item['price'];
            $invoice->items()->create($item + ['subtotal' => $sub]);
            $total += $sub;
        }
        $invoice->update(['total' => $total]);
    }
}
