<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gudang BMT')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
        .brand{display:flex;align-items:center;gap:16px;flex-shrink:0}
        .brand img{height:64px}
        .brand h1{font-size:26px;margin:0;color:#333}
        .page-title{margin-left:auto!important;text-align:right!important;flex-shrink:0;white-space:nowrap}

    .layout{display:flex;margin-top:var(--header-height)}
    /* Main content wrapper */
    .main-wrap{flex:1;padding:18px;margin-left:0;transition:margin-left 0.3s}
    @media (min-width: 640px) {
        .main-wrap{margin-left:256px}
    }
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
    
    /* Sidebar active state */
    .sidebar-nav a.active{background-color:rgba(255,212,59,0.2);border-left:4px solid var(--yellow)}
    </style>
</head>
<body>
    <header class="header">
        <div class="brand">
            <!-- Mobile menu button -->
            <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar" type="button" class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                </svg>
            </button>
            <img src="/logo.png" alt="logo" onerror="this.style.display='none'">
            <div>
                <div style="font-size:14px;color:#666">PT. Bastari Maju<br/><small style="color:#999">Tapin (Perseroda)</small></div>
            </div>
        </div>
        <h2 class="page-title" style="margin:0;padding-right:18px;color:#333;margin-left:auto;text-align:right;flex-shrink:0">@yield('page_title','Daftar Barang')</h2>
    </header>

    <!-- Sidebar -->
    <aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar" style="top: var(--header-height); height: calc(100% - var(--header-height)); background-color: var(--blue);">
        <div class="h-full px-3 py-4 overflow-y-auto" style="background-color: var(--blue);">
            <ul class="space-y-2 font-medium sidebar-nav">
                @php
                    $userRole = strtolower(session('role') ?? '');
                    $isAdmin = in_array($userRole, ['admin', 'penjaga gudang', 'pejaga gudang']);
                    $canCreateOrder = in_array($userRole, ['perencanaan', 'penjaga gudang', 'pejaga gudang', 'admin']);
                    $canValidateOrder = $userRole === 'umum';
                    $canValidateFinal = $userRole === 'keuangan';
                @endphp
                
                <li>
                    @if($userRole === 'admin')
                        <a href="{{ route('dashboard.admin') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                    @elseif($userRole === 'penjaga gudang' || $userRole === 'pejaga gudang')
                        <a href="{{ route('dashboard.gudang') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                    @elseif($userRole === 'direktur')
                        <a href="{{ route('dashboard.direktur') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                    @elseif($userRole === 'umum')
                        <a href="{{ route('dashboard.umum') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                    @elseif($userRole === 'perencanaan')
                        <a href="{{ route('dashboard.perencanaan') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                    @elseif($userRole === 'keuangan')
                        <a href="{{ route('dashboard.keuangan') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                    @else
                    <a href="{{ route('dashboard.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                    @endif
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                            <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                            <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                        </svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('barang.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                            <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Daftar Barang</span>
                    </a>
                </li>
                @if($isAdmin)
                <li>
                    <a href="{{ route('barang-rusak.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m17.418 3.623-.018-.008a6.713 6.713 0 0 0-2.4-.569V2h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2H9.89A6.977 6.977 0 0 1 12 8v5h-2V8A5 5 0 1 0 0 8v6a1 1 0 0 0 1 1h8v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-4h6a1 1 0 0 0 1-1V8a5 5 0 0 0-2.582-4.377ZM6 12H4a1 1 0 0 1 0-2h2a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Barang Rusak</span>
                    </a>
                </li>
                @endif
                @if($canCreateOrder)
                <li>
                    <a href="{{ route('order.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                            <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Order</span>
                    </a>
                </li>
                @endif
                @if($canValidateOrder)
                <li>
                    <a href="{{ route('order.confirm') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                            <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Validasi Order</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('rekap.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333;">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                            <path d="M17 5.923A1 1 0 0 0 16 5h-3V4a4 4 0 1 0-8 0v1H2a1 1 0 0 0-1 .923L.086 17.846A2 2 0 0 0 2.08 20h13.84a2 2 0 0 0 1.994-2.153L17 5.923ZM7 9a1 1 0 0 1-2 0V7h2v2Zm0-5a2 2 0 1 1 4 0v1H7V4Zm6 5a1 1 0 1 1-2 0V7h2v2Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Rekap SR/GM</span>
                    </a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="w-full flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group" style="background-color: #fff; color: #333; border: none; cursor: pointer;">
                            <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap text-left">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main content -->
    <div class="layout">
        <main class="main-wrap">
            <div class="panel">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-30 sm:hidden hidden" data-drawer-backdrop="default-sidebar"></div>

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
                clearTimeout(window._hdrTimer);
                window._hdrTimer = setTimeout(setHeaderHeight, 120);
            });
        })();

        // Sidebar toggle functionality
        (function() {
            const sidebar = document.getElementById('default-sidebar');
            const toggleButton = document.querySelector('[data-drawer-toggle="default-sidebar"]');
            const overlay = document.getElementById('sidebar-overlay');
            
            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
            
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
            
            if (toggleButton) {
                toggleButton.addEventListener('click', toggleSidebar);
            }
            
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = toggleButton && toggleButton.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 640) {
                    if (!sidebar.classList.contains('-translate-x-full')) {
                        closeSidebar();
                    }
                }
            });
        })();
    </script>
</body>
</html>
