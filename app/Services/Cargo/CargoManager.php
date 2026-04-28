<?php

namespace App\Services\Cargo;

use App\Services\Cargo\Contracts\CargoProvider;
use App\Services\Cargo\Providers\FakeCargoProvider;
use InvalidArgumentException;

class CargoManager
{
    /**
     * İlgili kargo sağlayıcısını resolve eder.
     * @param string $carrier (Örn: 'FAKE', 'ARAS', 'MNG')
     * @return CargoProvider
     */
    public function resolve(string $carrier): CargoProvider
    {
        return match (strtoupper($carrier)) {
            'FAKE' => new FakeCargoProvider(),
            // 'ARAS' => new ArasCargoProvider(),
            // 'MNG' => new MngCargoProvider(),
            default => throw new InvalidArgumentException("Unsupported cargo carrier: {$carrier}"),
        };
    }

    /**
     * Varsayılan kargo sağlayıcısını döndürür.
     */
    public function default(): CargoProvider
    {
        return $this->resolve(config('cargo.default', 'FAKE'));
    }
}
