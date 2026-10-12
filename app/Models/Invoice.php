<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['number', 'type', 'invoice_date', 'customer_id', 'photographer_id', 'title', 'total', 'notes'];
    protected $casts = ['invoice_date' => 'date'];

    public function items() { return $this->hasMany(InvoiceItem::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function photographer() { return $this->belongsTo(Photographer::class); }

    public function scopeOfType($q, string $type) { return $q->where('type', $type); }
    public function scopeBetweenDates($q, $from, $to) { return $q->whereBetween('invoice_date', [$from, $to]); }

    /** رقم تسلسلي آمن حتى بعد حذف فواتير (يعتمد على آخر رقم لا على العدد) */
    public static function nextNumber(string $type): string
    {
        $prefix = ($type === 'income' ? 'IN' : 'EX').'-'.now()->year.'-';
        $last = static::where('number', 'like', $prefix.'%')->orderByDesc('number')->value('number');
        $n = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix.str_pad($n, 5, '0', STR_PAD_LEFT);
    }
}
