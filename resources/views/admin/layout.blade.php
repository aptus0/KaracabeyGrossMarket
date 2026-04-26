<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Admin') | Karacabey Gross Market</title>
    <style nonce="{{ request()->attributes->get('csp_nonce') }}">
        :root { --bg:#f6f8f7; --ink:#17231d; --muted:#5b6b64; --line:#d9e3dd; --brand:#207a4d; --white:#fff; --danger:#b42318; }
        * { box-sizing:border-box; }
        body { margin:0; min-width:320px; background:var(--bg); color:var(--ink); font-family:Arial, Helvetica, sans-serif; }
        a { color:inherit; text-decoration:none; }
        input, textarea, select, button { font:inherit; }
        .shell { display:grid; grid-template-columns:240px minmax(0, 1fr); min-height:100vh; }
        .side { border-right:1px solid var(--line); background:var(--white); padding:18px; }
        .brand { display:grid; gap:2px; margin-bottom:20px; font-weight:800; }
        .brand span { color:var(--brand); font-size:13px; }
        .brand strong { font-size:20px; }
        .nav { display:grid; gap:6px; }
        .nav a, .logout button { display:flex; align-items:center; min-height:40px; border-radius:8px; padding:0 10px; color:var(--muted); font-weight:800; }
        .nav a:hover, .logout button:hover { background:#edf4f0; color:var(--ink); }
        .logout { margin-top:18px; }
        .logout button { width:100%; border:0; background:transparent; cursor:pointer; }
        .main { padding:26px; }
        .top { display:flex; justify-content:space-between; gap:16px; align-items:end; margin-bottom:18px; }
        .top h1 { margin:0; font-size:30px; line-height:1.1; }
        .eyebrow { margin:0 0 8px; color:var(--brand); font-size:13px; font-weight:800; text-transform:uppercase; }
        .grid { display:grid; grid-template-columns:repeat(5, minmax(0, 1fr)); gap:12px; }
        .card, .panel { border:1px solid var(--line); border-radius:8px; background:var(--white); }
        .card { min-height:96px; padding:14px; }
        .card span { display:block; color:var(--muted); font-size:13px; font-weight:800; }
        .card strong { display:block; margin-top:10px; font-size:26px; }
        .panel { padding:16px; margin-top:16px; overflow-x:auto; }
        .actions { display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
        .btn { display:inline-flex; align-items:center; justify-content:center; min-height:40px; border:1px solid var(--line); border-radius:8px; padding:0 12px; background:var(--white); font-weight:800; cursor:pointer; }
        .btn.primary { border-color:var(--brand); background:var(--brand); color:#fff; }
        .btn.danger { color:var(--danger); }
        .status { border:1px solid #b8dfc7; border-radius:8px; background:#eefaf2; padding:10px 12px; margin-bottom:14px; color:#14532d; font-weight:800; }
        table { width:100%; border-collapse:collapse; min-width:720px; }
        th, td { border-bottom:1px solid var(--line); padding:12px 10px; text-align:left; vertical-align:top; }
        th { color:var(--muted); font-size:13px; }
        td small { color:var(--muted); }
        .form { display:grid; gap:12px; max-width:760px; }
        .form label { display:grid; gap:6px; color:var(--muted); font-size:13px; font-weight:800; }
        .form input, .form textarea, .form select { width:100%; min-height:42px; border:1px solid var(--line); border-radius:8px; padding:9px 11px; background:#fff; color:var(--ink); }
        .form textarea { min-height:110px; resize:vertical; }
        .check-row { display:flex; gap:8px; align-items:center; color:var(--ink); }
        .check-row input { width:auto; min-height:auto; }
        .pagination { margin-top:14px; }
        @media (max-width:900px) { .shell { grid-template-columns:1fr; } .side { position:static; } .grid { grid-template-columns:repeat(2, minmax(0, 1fr)); } }
        @media (max-width:560px) { .main { padding:16px; } .top { align-items:stretch; flex-direction:column; } .grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="side">
            <a class="brand" href="{{ route('admin.dashboard') }}">
                <span>Karacabey</span>
                <strong>Gross Admin</strong>
            </a>
            <nav class="nav" aria-label="Admin menu">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.products.index') }}">Urunler</a>
                <a href="{{ route('admin.categories.index') }}">Kategoriler</a>
                <a href="{{ route('admin.orders.index') }}">Siparisler</a>
                <a href="{{ route('admin.payments.index') }}">Odemeler</a>
                <a href="{{ route('admin.users.index') }}">Kullanicilar</a>
                <a href="{{ route('admin.pages.index') }}">Sayfalar</a>
                <a href="{{ route('admin.homepage-blocks.index') }}">Ana Sayfa</a>
                <a href="{{ route('admin.campaigns.index') }}">Kampanya</a>
                <a href="{{ route('admin.marketing.edit') }}">SEO / Pixel</a>
            </nav>
            <form class="logout" action="{{ route('admin.logout') }}" method="post">
                @csrf
                <button type="submit">Cikis</button>
            </form>
        </aside>
        <main class="main">
            @if(session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
