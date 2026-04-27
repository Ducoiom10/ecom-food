{{-- Mobile header cho client layout --}}
<header class="sticky top-0 z-40 bg-[#FAFAF8] border-b-2 border-[#1C1C1C] px-4 pt-3 pb-3">
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('client.home') }}" class="flex items-center gap-2">
            <div
                class="w-8 h-8 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-lg flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C]">
                <span class="text-white text-xs">🍜</span>
            </div>
            <span class="font-black text-[#1C1C1C] tracking-tight">Ba Anh Em</span>
        </a>

        <div class="relative" id="mob-branch-wrap">
            <button onclick="toggleMobBranch(event)"
                class="flex items-center gap-1 text-xs border-2 border-[#1C1C1C] rounded-lg px-2 py-1 bg-white shadow-[2px_2px_0px_#1C1C1C]">
                <span>📍</span>
                <span class="max-w-[90px] truncate font-medium" id="mob-branch-label">Chi nhánh Quận 1</span>
                <span>▾</span>
            </button>
            <div id="mob-branch-menu"
                class="hidden absolute right-0 top-full mt-1 w-52 bg-white border-2 border-[#1C1C1C] rounded-xl shadow-[4px_4px_0px_#1C1C1C] z-50">
                @foreach ([['name' => 'Chi nhánh Quận 1', 'status' => 'open'], ['name' => 'Chi nhánh Quận 3', 'status' => 'open'], ['name' => 'Chi nhánh Bình Thạnh', 'status' => 'open'], ['name' => 'Chi nhánh Gò Vấp', 'status' => 'closed']] as $b)
                    <button
                        onclick="selectBranch('mob','{{ $b['name'] }}',{{ $b['status'] === 'closed' ? 'true' : 'false' }})"
                        class="w-full text-left px-3 py-2 flex items-center gap-2 hover:bg-orange-50 first:rounded-t-lg last:rounded-b-lg text-sm {{ $b['status'] === 'closed' ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $b['status'] === 'closed' ? 'disabled' : '' }}>
                        <div class="w-2 h-2 rounded-full {{ $b['status'] === 'open' ? 'bg-green-500' : 'bg-red-500' }}">
                        </div>
                        {{ $b['name'] }}
                    </button>
                @endforeach
            </div>
        </div>

        @auth
            <div class="flex items-center gap-2">
                {{-- Notification bell --}}
                <div class="relative" id="notif-wrap">
                    <button onclick="toggleNotif(event)"
                        class="relative w-8 h-8 border-2 border-[#1C1C1C] rounded-lg bg-white flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C]">
                        <span class="text-sm">🔔</span>
                        <span id="notif-badge"
                            class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
                    </button>
                    <div id="notif-menu"
                        class="hidden absolute right-0 top-full mt-1 w-72 bg-white border-2 border-[#1C1C1C] rounded-xl shadow-[4px_4px_0px_#1C1C1C] z-50 max-h-80 overflow-y-auto">
                        <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                            <span class="font-black text-sm text-[#1C1C1C]">Thông báo</span>
                            <a href="{{ route('client.notifications') }}" class="text-xs text-[#FF6B35] font-bold">Xem tất
                                cả</a>
                        </div>
                        <div id="notif-list" class="divide-y divide-gray-100">
                            <div class="p-4 text-center text-gray-400 text-xs">Đang tải...</div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('client.profile') }}"
                    class="relative w-8 h-8 border-2 border-[#1C1C1C] rounded-lg bg-[#FF6B35] flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] text-white font-black text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </a>
            </div>
        @else
            <a href="{{ route('login') }}"
                class="text-xs font-black text-[#FF6B35] border-2 border-[#FF6B35] px-2 py-1 rounded-lg">Đăng nhập</a>
        @endauth
    </div>

    <div class="flex gap-2 mb-3">
        <button onclick="setMode('delivery')" id="mob-btn-delivery"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C] transition-all">
            🛵 Giao hàng
        </button>
        <button onclick="setMode('pickup')" id="mob-btn-pickup"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] transition-all">
            🏪 Tự đến lấy
        </button>
    </div>

    <form action="{{ route('client.menu') }}" method="GET" class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" name="search" placeholder="Tìm món ăn, đồ uống..."
            class="w-full pl-9 pr-4 py-2.5 border-2 border-[#1C1C1C] rounded-xl bg-white shadow-[2px_2px_0px_#1C1C1C] text-sm outline-none focus:border-[#FF6B35] transition-all" />
    </form>
</header>

@auth
    <script>
        function toggleNotif(e) {
            e.stopPropagation();
            const menu = document.getElementById('notif-menu');
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                loadNotifications();
            }
        }

        async function loadNotifications() {
            try {
                const res = await fetch('{{ route('client.notifications.unread-count') }}');
                const data = await res.json();
                const badge = document.getElementById('notif-badge');
                const list = document.getElementById('notif-list');

                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                if (data.recent.length === 0) {
                    list.innerHTML = '<div class="p-4 text-center text-gray-400 text-xs">Chưa có thông báo</div>';
                    return;
                }

                list.innerHTML = data.recent.map(n => `
      <a href="{{ route('client.notifications') }}" class="block p-3 hover:bg-orange-50 transition-colors ${n.is_read ? 'opacity-60' : ''}">
        <p class="font-bold text-[#1C1C1C] text-xs">${n.title}</p>
        <p class="text-gray-500 text-xs mt-0.5 line-clamp-1">${n.body}</p>
        <p class="text-gray-400 text-[10px] mt-1">${n.created_at}</p>
      </a>
    `).join('');
            } catch (e) {
                console.error(e);
            }
        }

        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('notif-wrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('notif-menu').classList.add('hidden');
            }
        });

        // Load badge on page load
        loadNotifications();
    </script>
@endauth
