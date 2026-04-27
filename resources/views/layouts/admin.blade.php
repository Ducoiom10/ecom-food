<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ba Anh Em Admin - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #0F0F0F;
            color: white;
            font-family: 'Segoe UI', sans-serif;
        }

        .neo-shadow {
            box-shadow: 4px 4px 0px #333;
        }

        /* Skeleton shimmer */
        @keyframes shimmer {
            0% {
                background-position: -800px 0
            }

            100% {
                background-position: 800px 0
            }
        }

        .skeleton {
            background: linear-gradient(90deg, #1e1e1e 25%, #2a2a2a 50%, #1e1e1e 75%);
            background-size: 800px 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 12px;
        }

        /* Tooltip cho sidebar ở lg breakpoint (icon-only) */
        .nav-item {
            position: relative;
        }

        @media (min-width: 1024px) and (max-width: 1279px) {
            .nav-item .nav-tooltip {
                display: block;
                opacity: 0;
                pointer-events: none;
                position: absolute;
                left: calc(100% + 10px);
                top: 50%;
                transform: translateY(-50%);
                background: #1C1C1C;
                color: white;
                font-size: 12px;
                font-weight: 700;
                padding: 5px 12px;
                border-radius: 8px;
                white-space: nowrap;
                transition: opacity .15s ease;
                z-index: 999;
                border: 1px solid #444;
            }

            .nav-item .nav-tooltip::before {
                content: '';
                position: absolute;
                right: 100%;
                top: 50%;
                transform: translateY(-50%);
                border: 5px solid transparent;
                border-right-color: #444;
            }

            .nav-item:hover .nav-tooltip {
                opacity: 1;
                pointer-events: auto;
            }
        }

        @media not all and (min-width: 1024px) and (max-width: 1279px) {
            .nav-item .nav-tooltip {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-[#0F0F0F]">

    <div class="min-h-screen flex">

        {{-- Mobile overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

        {{-- ===== SIDEBAR ===== --}}
        <aside id="sidebar"
            class="fixed lg:static inset-y-0 left-0 z-50 flex flex-col bg-[#1A1A1A] border-r-2 border-[#333] transition-transform duration-300
           w-64 -translate-x-full lg:translate-x-0 lg:w-20 xl:w-60">

            {{-- Logo --}}
            <div class="p-4 border-b-2 border-[#333] flex items-center gap-3 min-h-[72px]">
                <div
                    class="w-9 h-9 bg-[#FF6B35] border-2 border-[#FF6B35] rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-lg">🍜</span>
                </div>
                <div class="xl:block hidden">
                    <div class="font-black text-white text-sm">Ba Anh Em</div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Admin Portal</div>
                    <button onclick="toggleSidebar()"
                        class="ml-auto lg:hidden text-gray-400 hover:text-white text-lg">✕</button>
                </div>

                {{-- Nav --}}
                <nav class="flex-1 p-2 space-y-1 overflow-y-auto">
                    @php
                        $navItems = [
                            [
                                'route' => 'admin.kds',
                                'icon' => '👨🍳',
                                'label' => 'Bếp KDS',
                                'color' => 'bg-orange-500',
                                'perm' => 'view_kds',
                            ],
                            [
                                'route' => 'admin.smartprep',
                                'icon' => '🧠',
                                'label' => 'Smart Prep',
                                'color' => 'bg-purple-500',
                                'perm' => 'view_smartprep',
                            ],
                            [
                                'route' => 'admin.dispatch',
                                'icon' => '🚚',
                                'label' => 'Điều phối',
                                'color' => 'bg-blue-500',
                                'perm' => 'view_dispatch',
                            ],
                            [
                                'route' => 'admin.branch',
                                'icon' => '🍴',
                                'label' => 'Chi nhánh',
                                'color' => 'bg-green-500',
                                'perm' => 'view_branch',
                            ],
                            [
                                'route' => 'admin.super',
                                'icon' => '📊',
                                'label' => 'Super Admin',
                                'color' => 'bg-red-500',
                                'perm' => 'manage_permissions',
                            ],
                        ];
                    @endphp
                    @foreach ($navItems as $item)
                        @if (auth()->user()->hasPermission($item['perm']))
                            <a href="{{ route($item['route']) }}"
                                class="nav-item flex items-center gap-3 px-3 py-3 rounded-xl transition-all
                 {{ request()->routeIs($item['route']) ? $item['color'] . ' text-white shadow-lg' : 'text-gray-400 hover:bg-[#252525] hover:text-white' }}">
                                <span class="text-xl flex-shrink-0">{{ $item['icon'] }}</span>
                                {{-- Desktop xl: show label --}}
                                <span class="xl:block hidden text-sm font-bold">{{ $item['label'] }}</span>
                                {{-- Mobile: show label --}}
                                <span class="lg:hidden text-sm font-bold">{{ $item['label'] }}</span>
                                {{-- Tooltip chỉ hiện ở lg (icon-only mode) --}}
                                <span class="nav-tooltip">{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </nav>

                {{-- Back to app --}}
                <div class="p-2 border-t-2 border-[#333]">
                    <a href="{{ route('client.home') }}"
                        class="nav-item flex items-center gap-3 px-3 py-3 rounded-xl text-gray-400 hover:bg-[#252525] hover:text-white transition-all">
                        <span class="text-xl flex-shrink-0">←</span>
                        <span class="xl:block hidden text-sm font-bold">App khách hàng</span>
                        <span class="lg:hidden text-sm font-bold">App khách hàng</span>
                        <span class="nav-tooltip">App khách hàng</span>
                    </a>
                </div>
        </aside>

        {{-- ===== MAIN ===== --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top bar --}}
            <header
                class="bg-[#1A1A1A] border-b-2 border-[#333] px-4 lg:px-6 py-3 flex items-center justify-between flex-shrink-0 sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()"
                        class="lg:hidden w-9 h-9 bg-[#252525] border border-[#444] rounded-xl flex items-center justify-center text-gray-400 hover:text-white">
                        ☰
                    </button>
                    <div>
                        {{-- Typography: H1 admin = 18px font-black --}}
                        <h1 class="font-black text-white text-base lg:text-lg leading-tight">@yield('page_title', 'Admin')</h1>
                        <p class="text-gray-400 text-xs hidden sm:block mt-0.5">
                            Hệ thống quản trị Ba Anh Em ·
                            {{ now()->locale('vi')->isoFormat('dddd, D [tháng] M') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 lg:gap-3">
                        <div
                            class="hidden sm:flex items-center gap-2 bg-green-500/10 border border-green-500/30 px-3 py-1.5 rounded-lg">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-green-400 text-xs font-bold">Hoạt động</span>
                        </div>
                        {{-- Notification bell --}}
                        <button
                            class="relative w-8 h-8 bg-[#252525] border border-[#444] rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                            🔔
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-[#FF6B35] rounded-full text-[9px] text-white font-black flex items-center justify-center">3</span>
                        </button>
                        <div
                            class="w-8 h-8 bg-[#FF6B35] rounded-full flex items-center justify-center text-sm font-black cursor-pointer hover:bg-[#e55a25] transition-colors">
                            A</div>
            </header>

            {{-- Content --}}
            <div class="flex-1 overflow-auto">
                @yield('content')
            </div>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        </script>
        @stack('scripts')
</body>

</html>
