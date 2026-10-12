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

            $total = 0;
            foreach ($items as $item) {
                $sub = $item['quantity'] * $item['price'];
                $invoice->items()->create($item + ['subtotal' => $sub]);
                $total += $sub;
            }
            $invoice->update(['total' => $total]);

            return $invoice;
        });
    }
}
