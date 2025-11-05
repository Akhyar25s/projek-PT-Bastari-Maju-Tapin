<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gudang BMT')</title>
    <style>
        /* Basic layout to resemble the provided design */
        :root{
            --blue:#3fc0d6;
            --dark-blue:#2fb0c6;
            --accent:#a7e04b;
            --muted:#f6f8f9;
            --panel:#ffffff;
            --btn:#49c2d3;
            --yellow:#FFD43B;
            --yellow-dark:#FFB300;
            --header-height:88px; /* adjust if your header is taller/shorter */
        }
        html,body{height:100%;margin:0;font-family:Arial, Helvetica, sans-serif;background:var(--muted)}
    /* make header background opaque so it doesn't visually blend with panel borders */
    .header{background:var(--panel);padding:18px 20px;border-bottom:8px solid var(--blue);display:flex;align-items:center;justify-content:space-between;box-sizing:border-box}
    /* make header fixed so it never moves; height is set via --header-height (calculated on load) */
    .header{position:fixed;top:0;left:0;right:0;z-index:1000;height:var(--header-height);overflow:hidden}
        .brand{display:flex;align-items:center;gap:16px}
        .brand img{height:64px}
        .brand h1{font-size:26px;margin:0;color:#333}

    .layout{display:flex;margin-top:var(--header-height)}
    /* Make sidebar fixed so it doesn't follow when scrolling */
    .sidebar{width:210px;background:var(--blue);padding:22px 12px;box-sizing:border-box;position:fixed;left:0;top:var(--header-height);height:calc(100% - var(--header-height));overflow:auto}
        .sidebar .nav{display:flex;flex-direction:column;gap:14px}
        .nav a{display:block;background:#fff;color:#333;padding:12px 14px;border-radius:6px;text-decoration:none;font-weight:700;box-shadow:inset 0 -3px 0 rgba(0,0,0,0.03)}

    .main-wrap{flex:1;padding:18px;margin-left:210px}
    .panel{background:var(--panel);border:8px solid var(--accent);padding:18px;box-sizing:border-box;min-height:420px;display:flex;flex-direction:column}

        /* Search box area */
        .search-area{background:#fff;padding:16px;border:4px solid #f0f0f0;margin-bottom:14px;display:flex;align-items:center;gap:12px}
        .search-area input[type=text]{flex:1;padding:10px 12px;border-radius:18px;border:1px solid #ddd}
        .search-area button{padding:10px 18px;border-radius:6px;border:0;background:#4b4b4b;color:#fff}

    /* Table styles */
    table{width:100%;border-collapse:collapse;background:#fff}
    thead th{background:var(--blue);color:#fff;padding:12px;text-align:left}
    tbody td{padding:12px;border-bottom:1px solid #eee;color:#333}
    /* Make only the table area scrollable */
    .table-wrapper{overflow:auto;flex:1}
    .table-wrapper thead th{position:sticky;top:0;z-index:2}
    .action-btn{background:var(--btn);color:#fff;padding:8px 16px;border-radius:20px;text-decoration:none;display:inline-block}

    /* Yellow accents and CTA */
    .btn-cta{background:var(--yellow);color:#111;padding:8px 16px;border-radius:20px;text-decoration:none;font-weight:700;display:inline-block;box-shadow:0 4px 8px rgba(0,0,0,0.08)}
    .btn-cta:hover{background:var(--yellow-dark)}
    .badge-warning{background:var(--yellow-dark);color:#111;padding:4px 8px;border-radius:999px;font-weight:700;font-size:0.85rem;display:inline-block;margin-left:8px}
    .row-highlight{background:rgba(255,212,59,0.10)}
    .total-row{background:rgba(255,212,59,0.16);font-weight:700}
    .panel-urgent{border:4px solid var(--yellow)}
    .sidebar .nav a.active{box-shadow:inset 6px 0 0 var(--yellow)}

        /* Responsive */
    @media (max-width:800px){.sidebar{display:none}.layout{padding:12px}.main-wrap{margin-left:0}}
    </style>
</head>
<body>
    <header class="header">
        <div class="brand">
            <img src="/logo.png" alt="logo" onerror="this.style.display='none'">
            <div>
                <div style="font-size:14px;color:#666">PT. Bastari Maju<br/><small style="color:#999">Tapin (Perseroda)</small></div>
            </div>
        </div>
        <h2 style="margin:0;padding-right:18px;color:#333">@yield('page_title','Daftar Barang')</h2>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <nav class="nav">
                <a href="#">Dashboard</a>
                <a href="{{ route('barang.index') }}">Daftar Barang</a>
                <a href="#">Barang Rusak</a>
                <a href="#">Order</a>
                <a href="#">Rekap SR/GM</a>
                <a href="#">Pengaturan</a>
            </nav>
        </aside>

        <main class="main-wrap">
            <div class="panel">
                @yield('content')
            </div>
        </main>
    </div>
</body>
    <script>
        // Ensure CSS variable --header-height matches the rendered header height
        (function(){
            function setHeaderHeight(){
                var header = document.querySelector('.header');
                if(!header) return;
                var h = header.getBoundingClientRect().height;
                document.documentElement.style.setProperty('--header-height', h + 'px');
            }
            window.addEventListener('DOMContentLoaded', setHeaderHeight);
            window.addEventListener('load', setHeaderHeight);
            window.addEventListener('resize', function(){
                // small debounce
                clearTimeout(window._hdrTimer);
                window._hdrTimer = setTimeout(setHeaderHeight, 120);
            });
        })();
    </script>
</html>
