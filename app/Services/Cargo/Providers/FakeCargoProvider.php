<?php

namespace App\Services\Cargo\Providers;

use App\Models\Order;
use App\Services\Cargo\Contracts\CargoProvider;
use Illuminate\Support\Str;

class FakeCargoProvider implements CargoProvider
{
    public function createShipment(Order $order): array
    {
        // Gerçek API gecikmesini simüle edelim (eğer istenirse)
        // sleep(1);

        $trackingNumber = 'FAKE-' . strtoupper(Str::random(10));

        return [
            'tracking_number' => $trackingNumber,
            'tracking_url' => 'https://fake-cargo.com/track/' . $trackingNumber,
            'metadata' => [
                'provider_response' => 'SUCCESS',
                'timestamp' => now()->toIso8601String(),
                'provider_id' => random_int(10000, 99999),
            ],
        ];
    }

    public function track(string $trackingNumber): array
    {
        // Burada rastgele bir statü dönebiliriz ya da trackingNumber'a göre belli bir senaryo.
        // Geliştirme kolaylığı için %70 ihtimalle 'delivered', %30 ihtimalle 'in_transit' dönelim.
        $status = random_int(1, 100) > 30 ? 'delivered' : 'in_transit';

        return [
            'status' => $status,
            'metadata' => [
                'provider_response' => 'SUCCESS',
                'timestamp' => now()->toIso8601String(),
                'location' => 'Istanbul Hub',
            ],
        ];
    }
}
