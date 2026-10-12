<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photographer extends Model
{
    protected $fillable = ['name', 'phone', 'default_rate'];

    public function invoices() { return $this->hasMany(Invoice::class); }
}
