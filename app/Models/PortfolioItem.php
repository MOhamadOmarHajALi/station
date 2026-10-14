<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioItem extends Model
{
    protected $fillable = ['title', 'category', 'mime', 'image_data', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('id'); }

    /** رابط الصورة (الرقم في النهاية لتحديث الكاش عند تغيير الصورة) */
    public function url(): string
    {
        return "/media/portfolio/{$this->id}?v=".$this->updated_at?->timestamp;
    }
}
