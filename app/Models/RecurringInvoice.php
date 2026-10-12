<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringInvoice extends Model
{
    protected $fillable = ['type', 'title', 'amount', 'day_of_month', 'customer_id', 'photographer_id', 'active', 'last_run_on'];
    protected $casts = ['active' => 'boolean', 'last_run_on' => 'date'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function photographer() { return $this->belongsTo(Photographer::class); }
}
