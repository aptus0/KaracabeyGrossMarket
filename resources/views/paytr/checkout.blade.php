<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Karacabey Gross Market Guvenli Odeme</title>
    <link rel="stylesheet" href="{{ asset('assets/kgm-paytr.css') }}">
</head>
<body class="k0">
    <main class="k1" aria-label="PayTR guvenli odeme">
        <section class="k2">
            <div class="k3">
                <p class="k4">Karacabey Gross Market</p>
                <h1 class="k5">Guvenli Odeme</h1>
                <p class="k6">Siparis No: {{ $order->merchant_oid }}</p>
            </div>
            <strong class="k7">{{ number_format($order->total_cents / 100, 2, ',', '.') }} {{ $order->currency }}</strong>
        </section>

        <section class="k8">
            <iframe
                src="{{ $iframeSrc }}"
                id="paytriframe"
                title="PayTR guvenli odeme formu"
                frameborder="0"
                scrolling="no"
                class="k9"
                allow="payment"
            ></iframe>
        </section>
    </main>

    <script src="https://www.paytr.com/js/iframeResizer.min.js" defer></script>
    <script src="{{ asset('assets/kgm-paytr.js') }}" defer></script>
</body>
</html>
