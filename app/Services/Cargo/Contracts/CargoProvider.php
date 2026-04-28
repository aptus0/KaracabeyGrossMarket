<?php

namespace App\Services\Cargo\Contracts;

use App\Models\Order;

interface CargoProvider
{
    /**
     * Kargo firmasında gönderi oluşturur.
     * @param Order $order
     * @return array{tracking_number: string, tracking_url: string|null, metadata: array}
     */
    public function createShipment(Order $order): array;

    /**
     * Kargo takip numarasından güncel durumu sorgular.
     * @param string $trackingNumber
     * @return array{status: string, metadata: array}
     */
    public function track(string $trackingNumber): array;
}
