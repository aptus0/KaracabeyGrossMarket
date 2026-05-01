<?php

namespace App\Support;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantResolver
{
    public function resolve(Request $request): Tenant
    {
        $host = $request->getHost();
        $slug = $request->header('X-Tenant');

        $query = Tenant::query()->where('is_active', true);

        $tenant = $slug
            ? (clone $query)->where('slug', $slug)->first()
            : null;

        $tenant ??= (clone $query)->where('domain', $host)->first();
        $tenant ??= (clone $query)->where('slug', 'karacabey-gross-market')->first();

        return $tenant ?? Tenant::query()->create([
            'name' => 'Karacabey Gross Market',
            'slug' => 'karacabey-gross-market',
            'domain' => (string) config('commerce.primary_domain', 'karacabeygrossmarket.com'),
            'settings' => [
                'storefront_domain' => parse_url((string) config('commerce.domains.storefront'), PHP_URL_HOST),
                'admin_domain' => parse_url((string) config('commerce.domains.admin'), PHP_URL_HOST),
                'api_domain' => parse_url((string) config('commerce.domains.api'), PHP_URL_HOST),
                'cdn_domain' => parse_url((string) config('commerce.domains.cdn'), PHP_URL_HOST),
            ],
        ]);
    }
}
