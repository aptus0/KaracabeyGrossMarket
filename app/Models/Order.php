<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'merchant_oid',
        'checkout_ref',
        'status',
        'currency',
        'subtotal_cents',
        'shipping_cents',
        'discount_cents',
        'total_cents',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_city',
        'shipping_district',
        'shipping_address',
        'metadata',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * @return array<int, array{0: string, 1: string, 2: int}>
     */
    public function paytrBasket(): array
    {
        return $this->items->map(fn (OrderItem $item): array => [
            $item->name,
            number_format($item->unit_price_cents / 100, 2, '.', ''),
            $item->quantity,
        ])->values()->all();
    }
}
