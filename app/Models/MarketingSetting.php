<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'google_analytics_id',
        'google_ads_id',
        'google_ads_conversion_label',
        'google_site_verification',
        'meta_pixel_id',
        'extra',
    ];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
