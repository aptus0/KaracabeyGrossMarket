<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Str::random(32);
        $request->attributes->set('csp_nonce', $nonce);

        /** @var Response $response */
        $response = $next($request);

        $scriptSources  = ["'self'", "'nonce-{$nonce}'", 'https://www.paytr.com'];
        $styleSources   = ["'self'", "'nonce-{$nonce}'"];
        $connectSources = ["'self'", 'https://www.paytr.com', 'https://*.paytr.com'];
        $formSources    = ["'self'", 'https://www.paytr.com'];

        foreach ($this->viteDevelopmentSources() as $source) {
            $scriptSources[]  = $source;
            $styleSources[]   = $source;
            $connectSources[] = $source;
        }

        // In local dev: relax restrictions for HMR, inline scripts and mixed-scheme form actions
        if (app()->isLocal()) {
            $scriptSources[]  = "'unsafe-inline'";
            $styleSources[]   = "'unsafe-inline'";
            $formSources[]    = $request->getSchemeAndHttpHost();   // actual browser origin
            $formSources[]    = config('app.url');                  // APP_URL value
            $formSources[]    = 'http://localhost:8000';
            $formSources[]    = 'http://127.0.0.1:8000';
            $formSources[]    = 'https://karacabey-gross-market.test';
            $formSources[]    = 'http://karacabey-gross-market.test';
            $connectSources[] = 'http://web';
            $connectSources[] = 'http://kgm-nginx';
        }

        $frameAncestors = collect([
            parse_url((string) config('commerce.domains.storefront'), PHP_URL_SCHEME) && parse_url((string) config('commerce.domains.storefront'), PHP_URL_HOST)
                ? rtrim((string) config('commerce.domains.storefront'), '/')
                : null,
            parse_url((string) config('commerce.domains.admin'), PHP_URL_SCHEME) && parse_url((string) config('commerce.domains.admin'), PHP_URL_HOST)
                ? rtrim((string) config('commerce.domains.admin'), '/')
                : null,
        ])->filter()->unique()->implode(' ');

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-src 'self' https://www.paytr.com https://*.paytr.com",
            "frame-ancestors 'self' {$frameAncestors}",
            'script-src ' . implode(' ', array_unique($scriptSources)),
            'style-src '  . implode(' ', array_unique($styleSources)),
            "img-src 'self' data: https:",
            "font-src 'self' data:",
            'connect-src ' . implode(' ', array_unique($connectSources)),
            'form-action '  . implode(' ', array_unique($formSources)),
        ];

        if (app()->isProduction()) {
            $directives[] = 'upgrade-insecure-requests';
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        $response->headers->set('Content-Security-Policy', implode('; ', $directives));
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self), payment=(self "https://www.paytr.com")');

        return $response;
    }

    /**
     * Allow Vite's dev server only while a hot file is present in local development.
     *
     * @return array<int, string>
     */
    protected function viteDevelopmentSources(): array
    {
        if (! app()->isLocal()) {
            return [];
        }

        $hotFile = public_path('hot');

        if (! is_file($hotFile)) {
            return [];
        }

        $hotUrl = trim((string) file_get_contents($hotFile));
        $origin = $this->normalizeOrigin($hotUrl);

        if ($origin === null) {
            return [];
        }

        $sources = [$origin];
        $websocketOrigin = $this->toWebsocketOrigin($origin);

        if ($websocketOrigin !== null) {
            $sources[] = $websocketOrigin;
        }

        return $sources;
    }

    protected function normalizeOrigin(string $url): ?string
    {
        $parts = parse_url($url);

        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $origin = "{$parts['scheme']}://{$parts['host']}";

        if (! empty($parts['port'])) {
            $origin .= ":{$parts['port']}";
        }

        return $origin;
    }

    protected function toWebsocketOrigin(string $origin): ?string
    {
        if (str_starts_with($origin, 'https://')) {
            return 'wss://' . substr($origin, 8);
        }

        if (str_starts_with($origin, 'http://')) {
            return 'ws://' . substr($origin, 7);
        }

        return null;
    }
}
