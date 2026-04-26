<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'type',
        'title',
        'subtitle',
        'image_url',
        'link_url',
        'link_label',
        'payload',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
