<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('verifies paytr callback hash and marks payment as paid once', function (): void {
    config([
        'paytr.merchant_key' => 'merchant-key',
        'paytr.merchant_salt' => 'merchant-salt',
    ]);

    $tenant = Tenant::query()->create([
        'name' => 'Karacabey Gross Market',
        'slug' => 'karacabey-gross-market',
        'domain' => 'karacabeygrossmarket.com',
    ]);

    $order = Order::query()->create([
        'tenant_id' => $tenant->id,
        'merchant_oid' => 'KGM260426TEST',
        'status' => OrderStatus::AwaitingPayment,
        'currency' => 'TL',
        'subtotal_cents' => 12500,
        'shipping_cents' => 0,
        'discount_cents' => 0,
        'total_cents' => 12500,
        'customer_name' => 'Test Musteri',
        'customer_email' => 'test@example.com',
        'customer_phone' => '5551112233',
        'shipping_address' => 'Karacabey',
    ]);

    Payment::query()->create([
        'order_id' => $order->id,
        'provider' => 'paytr',
        'merchant_oid' => $order->merchant_oid,
        'status' => PaymentStatus::Pending,
        'amount_cents' => 12500,
        'currency' => 'TL',
    ]);

    $payload = [
        'merchant_oid' => $order->merchant_oid,
        'status' => 'success',
        'total_amount' => '12500',
        'payment_amount' => '12500',
        'payment_type' => 'card',
        'currency' => 'TL',
    ];
    $payload['hash'] = base64_encode(hash_hmac(
        'sha256',
        $payload['merchant_oid'].'merchant-salt'.$payload['status'].$payload['total_amount'],
        'merchant-key',
        true
    ));

    $this->post('/api/paytr/callback', $payload)
        ->assertOk()
        ->assertSeeText('OK');

    $this->post('/api/paytr/callback', $payload)
        ->assertOk()
        ->assertSeeText('OK');

    expect($order->fresh()->status)->toBe(OrderStatus::Paid)
        ->and($order->payment()->first()->status)->toBe(PaymentStatus::Paid);
});

it('rejects callback payloads with an invalid hash', function (): void {
    config([
        'paytr.merchant_key' => 'merchant-key',
        'paytr.merchant_salt' => 'merchant-salt',
    ]);

    $this->post('/api/paytr/callback', [
        'merchant_oid' => 'KGM260426BAD',
        'status' => 'success',
        'total_amount' => '12500',
        'hash' => 'bad-hash',
    ])->assertStatus(400);
});
