<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'brand',
        'barcode',
        'price_cents',
        'compare_at_price_cents',
        'stock_quantity',
        'image_url',
        'seo',
        'metadata',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'seo' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function formattedPrice(): string
    {
        return Number::currency($this->price_cents / 100, 'TRY', locale: 'tr');
    }
}
