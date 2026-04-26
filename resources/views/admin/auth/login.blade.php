<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin Giris | Karacabey Gross Market</title>
    <style nonce="{{ request()->attributes->get('csp_nonce') }}">
        body { min-width:320px; min-height:100vh; display:grid; place-items:center; margin:0; background:#f6f8f7; color:#17231d; font-family:Arial, Helvetica, sans-serif; }
        form { width:min(100% - 32px, 420px); display:grid; gap:14px; border:1px solid #d9e3dd; border-radius:8px; background:#fff; padding:22px; }
        h1 { margin:0; font-size:30px; }
        p { margin:0; color:#5b6b64; }
        label { display:grid; gap:6px; color:#5b6b64; font-size:13px; font-weight:800; }
        input { min-height:44px; border:1px solid #d9e3dd; border-radius:8px; padding:9px 11px; font:inherit; }
        button { min-height:44px; border:0; border-radius:8px; background:#207a4d; color:#fff; font:inherit; font-weight:800; cursor:pointer; }
        .error { color:#b42318; font-size:13px; font-weight:800; }
    </style>
</head>
<body>
    <form action="{{ route('admin.login.store') }}" method="post">
        @csrf
        <div>
            <p>Karacabey Gross Market</p>
            <h1>Admin Giris</h1>
        </div>
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror
        <label>
            E-posta
            <input name="email" type="email" autocomplete="email" required autofocus>
        </label>
        <label>
            Sifre
            <input name="password" type="password" autocomplete="current-password" required>
        </label>
        <button type="submit">Giris Yap</button>
    </form>
</body>
</html>
