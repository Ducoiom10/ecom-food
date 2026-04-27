<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ba Anh Em - @yield('title', 'Trang chủ')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    * { box-sizing: border-box; }
    body { background: #FAFAF8; font-family: 'Segoe UI', sans-serif; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .neo-shadow { box-shadow: 4px 4px 0px #1C1C1C; }
    .neo-shadow-sm { box-shadow: 2px 2px 0px #1C1C1C; }
    .neo-shadow-orange { box-shadow: 4px 4px 0px #FF6B35; }
  </style>
  @stack('styles')
</head>
<body class="bg-[#FAFAF8]">

{{-- ===== DESKTOP LAYOUT ===== --}}
<div class="hidden lg:flex min-h-screen">

  {{-- Desktop Left Sidebar --}}
  <aside class="w-64 xl:w-72 flex-shrink-0 bg-white border-r-2 border-[#1C1C1C] flex flex-col sticky top-0 h-screen">
    {{-- Logo --}}
    <div class="p-6 border-b-2 border-[#1C1C1C]">
      <a href="{{ route('client.home') }}" class="flex items-center gap-3">
        <div class="w-10 h-10 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center neo-shadow-sm">
          <span class="text-white text-lg">🍜</span>
        </div>
        <div>
          <div class="font-black text-[#1C1C1C] text-lg tracking-tight">Ba Anh Em</div>
          <div class="text-xs text-gray-400">F&B Ecosystem</div>
        </div>
      </a>
    </div>

    {{-- Branch + Mode --}}
    <div class="p-4 border-b-2 border-[#1C1C1C] space-y-3">
      {{-- Branch selector --}}
      <div class="relative" id="desk-branch-wrap">
        <button onclick="toggleDeskBranch()" class="w-full flex items-center gap-2 text-sm border-2 border-[#1C1C1C] rounded-xl px-3 py-2 bg-white neo-shadow-sm hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
          <span class="text-[#FF6B35]">📍</span>
          <span class="flex-1 text-left font-medium truncate" id="desk-branch-label">Chi nhánh Quận 1</span>
          <span class="text-gray-400" id="desk-branch-arrow">▾</span>
        </button>
        <div id="desk-branch-menu" class="hidden absolute left-0 top-full mt-1 w-full bg-white border-2 border-[#1C1C1C] rounded-xl neo-shadow z-50">
          @foreach([['name'=>'Chi nhánh Quận 1','status'=>'open'],['name'=>'Chi nhánh Quận 3','status'=>'open'],['name'=>'Chi nhánh Bình Thạnh','status'=>'open'],['name'=>'Chi nhánh Gò Vấp','status'=>'closed']] as $b)
          <button
            onclick="selectBranch('desk', '{{ $b['name'] }}', {{ $b['status']==='closed' ? 'true' : 'false' }})"
            class="w-full text-left px-3 py-2.5 flex items-center gap-2 hover:bg-orange-50 first:rounded-t-lg last:rounded-b-lg text-sm {{ $b['status']==='closed'?'opacity-50 cursor-not-allowed':'' }}"
            {{ $b['status']==='closed'?'disabled':'' }}>
            <div class="w-2 h-2 rounded-full {{ $b['status']==='open'?'bg-green-500':'bg-red-500' }}"></div>
            {{ $b['name'] }}
          </button>
          @endforeach
        </div>
      </div>

      {{-- Order mode --}}
      <div class="flex gap-2">
        <button onclick="setMode('delivery')" id="desk-btn-delivery" class="flex-1 flex items-center justify-center gap-1 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs font-bold bg-[#FF6B35] text-white neo-shadow-sm transition-all">
          🛵 Giao hàng
        </button>
        <button onclick="setMode('pickup')" id="desk-btn-pickup" class="flex-1 flex items-center justify-center gap-1 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs font-bold bg-white text-[#1C1C1C] neo-shadow-sm transition-all">
          🏪 Tự lấy
        </button>
      </div>
    </div>

    {{-- Search --}}
    <div class="px-4 py-3 border-b-2 border-[#1C1C1C]">
      <form action="{{ route('client.menu') }}" method="GET" class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" name="search" placeholder="Tìm món ăn..."
          class="w-full pl-9 pr-4 py-2.5 border-2 border-[#1C1C1C] rounded-xl bg-white text-sm outline-none focus:border-[#FF6B35] transition-all" />
      </form>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 p-4 space-y-1">
      @php
        $cartCount = count(session('cart', []));
        $navItems = [
          ['route'=>'client.home',    'icon'=>'🏠', 'label'=>'Trang chủ'],
          ['route'=>'client.menu',    'icon'=>'🍽️', 'label'=>'Thực đơn'],
          ['route'=>'client.cart',    'icon'=>'🛒', 'label'=>'Giỏ hàng', 'badge'=>$cartCount],
          ['route'=>'client.profile', 'icon'=>'👤', 'label'=>'Tài khoản'],
          ['route'=>'client.group-order','icon'=>'👥','label'=>'Đặt nhóm'],
        ];
      @endphp
      @foreach($navItems as $item)
      <a href="{{ route($item['route']) }}"
        class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all relative {{ request()->routeIs($item['route']) ? 'bg-[#FF6B35] text-white neo-shadow-sm' : 'text-gray-600 hover:bg-orange-50 hover:text-[#FF6B35]' }}">
        <span class="text-xl">{{ $item['icon'] }}</span>
        {{ $item['label'] }}
        @if(isset($item['badge']) && $item['badge'] > 0)
        <span class="ml-auto w-5 h-5 bg-[#FFD23F] border border-[#1C1C1C] rounded-full text-[10px] text-[#1C1C1C] font-black flex items-center justify-center">{{ $item['badge'] }}</span>
        @endif
      </a>
      @endforeach
    </nav>

    {{-- Admin link --}}
    <div class="p-4 border-t-2 border-[#1C1C1C]">
      <a href="{{ route('admin.kds') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm bg-[#1C1C1C] text-white neo-shadow-orange hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
        <span class="text-xl">🍳</span> Admin Portal
      </a>
    </div>
  </aside>

  {{-- Desktop Main Content --}}
  <div class="flex-1 flex flex-col min-w-0">
    {{-- Top bar --}}
    <header class="sticky top-0 z-40 bg-white border-b-2 border-[#1C1C1C] px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="font-black text-[#1C1C1C] text-xl">@yield('page_heading', 'Trang chủ')</h1>
        <p class="text-gray-400 text-xs mt-0.5">{{ now()->locale('vi')->isoFormat('dddd, D [tháng] M, YYYY') }}</p>
      </div>
      <div class="flex items-center gap-4">
        <button class="relative w-10 h-10 border-2 border-[#1C1C1C] rounded-xl bg-white flex items-center justify-center neo-shadow-sm hover:shadow-none transition-all">
          🔔
          <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#FF6B35] border border-white rounded-full text-[9px] text-white font-bold flex items-center justify-center">3</span>
        </button>
        <a href="{{ route('client.profile') }}" class="flex items-center gap-2 border-2 border-[#1C1C1C] rounded-xl px-3 py-2 bg-white neo-shadow-sm hover:shadow-none transition-all">
          <div class="w-7 h-7 bg-[#FF6B35] rounded-lg flex items-center justify-center text-white text-sm">👤</div>
          <span class="font-bold text-sm text-[#1C1C1C]">{{ auth()->user()?->name ?? 'Tài khoản' }}</span>
        </a>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto">
      @yield('content')
    </main>
  </div>
</div>

{{-- ===== MOBILE LAYOUT ===== --}}
<div class="lg:hidden flex flex-col min-h-screen">

  {{-- Mobile Header --}}
  <header class="sticky top-0 z-40 bg-[#FAFAF8] border-b-2 border-[#1C1C1C] px-4 pt-3 pb-3">
    <div class="flex items-center justify-between mb-3">
      <a href="{{ route('client.home') }}" class="flex items-center gap-2">
        <div class="w-8 h-8 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-lg flex items-center justify-center neo-shadow-sm">
          <span class="text-white text-xs">🍜</span>
        </div>
        <span class="font-black text-[#1C1C1C] tracking-tight">Ba Anh Em</span>
      </a>

      <div class="relative" id="mob-branch-wrap">
        <button onclick="toggleMobBranch(event)" class="flex items-center gap-1 text-xs border-2 border-[#1C1C1C] rounded-lg px-2 py-1 bg-white neo-shadow-sm">
          <span>📍</span>
          <span class="max-w-[90px] truncate font-medium" id="mob-branch-label">Chi nhánh Quận 1</span>
          <span>▾</span>
        </button>
        <div id="mob-branch-menu" class="hidden absolute right-0 top-full mt-1 w-52 bg-white border-2 border-[#1C1C1C] rounded-xl neo-shadow z-50">
          @foreach([['name'=>'Chi nhánh Quận 1','status'=>'open'],['name'=>'Chi nhánh Quận 3','status'=>'open'],['name'=>'Chi nhánh Bình Thạnh','status'=>'open'],['name'=>'Chi nhánh Gò Vấp','status'=>'closed']] as $b)
          <button
            onclick="selectBranch('mob', '{{ $b['name'] }}', {{ $b['status']==='closed' ? 'true' : 'false' }})"
            class="w-full text-left px-3 py-2 flex items-center gap-2 hover:bg-orange-50 first:rounded-t-lg last:rounded-b-lg text-sm {{ $b['status']==='closed'?'opacity-50 cursor-not-allowed':'' }}"
            {{ $b['status']==='closed'?'disabled':'' }}>
            <div class="w-2 h-2 rounded-full {{ $b['status']==='open'?'bg-green-500':'bg-red-500' }}"></div>
            {{ $b['name'] }}
          </button>
          @endforeach
        </div>
      </div>

      <button class="relative w-8 h-8 border-2 border-[#1C1C1C] rounded-lg bg-white flex items-center justify-center neo-shadow-sm">
        🔔
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-[#FF6B35] border border-white rounded-full text-[9px] text-white font-bold flex items-center justify-center">3</span>
      </button>
    </div>

    <div class="flex gap-2 mb-3">
      <button onclick="setMode('delivery')" id="mob-btn-delivery" class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-[#FF6B35] text-white neo-shadow-sm transition-all">
        🛵 Giao hàng
      </button>
      <button onclick="setMode('pickup')" id="mob-btn-pickup" class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-white text-[#1C1C1C] neo-shadow-sm transition-all">
        🏪 Tự đến lấy
      </button>
    </div>

    <form action="{{ route('client.menu') }}" method="GET" class="relative">
      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
      <input type="text" name="search" placeholder="Tìm món ăn, đồ uống..."
        class="w-full pl-9 pr-4 py-2.5 border-2 border-[#1C1C1C] rounded-xl bg-white neo-shadow-sm text-sm outline-none focus:border-[#FF6B35] transition-all" />
    </form>
  </header>

  <main class="flex-1 overflow-y-auto pb-24">
    @yield('content')
  </main>

  {{-- Admin quick access --}}
  <div class="fixed bottom-20 right-4 z-40">
    <a href="{{ route('admin.kds') }}" class="bg-[#1C1C1C] text-white text-xs px-3 py-2 rounded-xl border-2 border-[#1C1C1C] neo-shadow-orange font-bold block">
      🍳 Admin
    </a>
  </div>

  {{-- Bottom Nav --}}
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-[#1C1C1C] z-40">
    <div class="flex items-center justify-around px-2 py-2 max-w-lg mx-auto">
      @foreach([['route'=>'client.home','icon'=>'🏠','label'=>'Trang chủ'],['route'=>'client.menu','icon'=>'🍽️','label'=>'Thực đơn'],['route'=>'client.cart','icon'=>'🛒','label'=>'Giỏ hàng','badge'=>count(session('cart',[]))],['route'=>'client.profile','icon'=>'👤','label'=>'Tài khoản']] as $item)
      <a href="{{ route($item['route']) }}"
        class="flex flex-col items-center gap-0.5 px-4 py-1.5 rounded-xl transition-all relative {{ request()->routeIs($item['route']) ? 'bg-[#FF6B35] text-white neo-shadow-sm' : 'text-gray-500 hover:text-[#FF6B35]' }}">
        <span class="text-xl">{{ $item['icon'] }}</span>
        <span class="text-[10px] font-bold">{{ $item['label'] }}</span>
        @if(isset($item['badge']) && $item['badge'] > 0)
        <span class="absolute -top-1 right-1 w-4 h-4 bg-[#FFD23F] border border-[#1C1C1C] rounded-full text-[9px] text-[#1C1C1C] font-black flex items-center justify-center">{{ $item['badge'] }}</span>
        @endif
      </a>
      @endforeach
    </div>
  </nav>
</div>

{{-- Toast notifications --}}
<div id="toast-container" class="fixed top-4 right-4 z-[9999] space-y-2 pointer-events-none"></div>
@if(session('success'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast('{{ session('success') }}','success'));</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded',()=>showToast('{{ session('error') }}','error'));</script>
@endif

<script>
function showToast(msg, type = 'success') {
  const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500' };
  const icons  = { success: '✅', error: '❌', info: 'ℹ️' };
  const t = document.createElement('div');
  t.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C] text-white text-sm font-bold ${colors[type]} translate-x-full transition-transform duration-300`;
  t.innerHTML = `<span>${icons[type]}</span><span>${msg}</span>`;
  document.getElementById('toast-container').appendChild(t);
  requestAnimationFrame(() => { requestAnimationFrame(() => { t.classList.remove('translate-x-full'); }); });
  setTimeout(() => { t.classList.add('translate-x-full'); setTimeout(() => t.remove(), 300); }, 3000);
}

// Đóng dropdown khi click ra ngoài
document.addEventListener('click', function(e) {
  if (!document.getElementById('mob-branch-wrap')?.contains(e.target)) {
    document.getElementById('mob-branch-menu')?.classList.add('hidden');
  }
  if (!document.getElementById('desk-branch-wrap')?.contains(e.target)) {
    document.getElementById('desk-branch-menu')?.classList.add('hidden');
  }
});

function toggleMobBranch(e) {
  e.stopPropagation();
  document.getElementById('mob-branch-menu').classList.toggle('hidden');
}

function toggleDeskBranch() {
  document.getElementById('desk-branch-menu').classList.toggle('hidden');
}

function selectBranch(type, name, isClosed) {
  if (isClosed) return;
  if (type === 'mob') {
    document.getElementById('mob-branch-label').textContent = name;
    document.getElementById('mob-branch-menu').classList.add('hidden');
  } else {
    document.getElementById('desk-branch-label').textContent = name;
    document.getElementById('desk-branch-menu').classList.add('hidden');
  }
}

function setMode(mode) {
  const ids = { delivery: ['desk-btn-delivery','mob-btn-delivery'], pickup: ['desk-btn-pickup','mob-btn-pickup'] };
  ['delivery','pickup'].forEach(m => {
    ids[m].forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      if (m === mode) {
        el.classList.add(m==='delivery'?'bg-[#FF6B35]':'bg-[#1C1C1C]', 'text-white');
        el.classList.remove('bg-white','text-[#1C1C1C]');
      } else {
        el.classList.remove('bg-[#FF6B35]','bg-[#1C1C1C]','text-white');
        el.classList.add('bg-white','text-[#1C1C1C]');
      }
    });
  });
}
</script>
@stack('scripts')
</body>
</html>
