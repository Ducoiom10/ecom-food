{{-- Desktop sidebar cho client layout --}}
<aside class="w-64 xl:w-72 flex-shrink-0 bg-white border-r-2 border-[#1C1C1C] flex flex-col sticky top-0 h-screen">

  {{-- Logo --}}
  <div class="p-6 border-b-2 border-[#1C1C1C]">
    <a href="{{ route('client.home') }}" class="flex items-center gap-3">
      <div class="w-10 h-10 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C]">
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
    <div class="relative" id="desk-branch-wrap">
      <button onclick="toggleDeskBranch()" class="w-full flex items-center gap-2 text-sm border-2 border-[#1C1C1C] rounded-xl px-3 py-2 bg-white shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
        <span class="text-[#FF6B35]">📍</span>
        <span class="flex-1 text-left font-medium truncate" id="desk-branch-label">Chi nhánh Quận 1</span>
        <span class="text-gray-400">▾</span>
      </button>
      <div id="desk-branch-menu" class="hidden absolute left-0 top-full mt-1 w-full bg-white border-2 border-[#1C1C1C] rounded-xl shadow-[4px_4px_0px_#1C1C1C] z-50">
        @foreach([['name'=>'Chi nhánh Quận 1','status'=>'open'],['name'=>'Chi nhánh Quận 3','status'=>'open'],['name'=>'Chi nhánh Bình Thạnh','status'=>'open'],['name'=>'Chi nhánh Gò Vấp','status'=>'closed']] as $b)
        <button onclick="selectBranch('desk','{{ $b['name'] }}',{{ $b['status']==='closed'?'true':'false' }})"
          class="w-full text-left px-3 py-2.5 flex items-center gap-2 hover:bg-orange-50 first:rounded-t-lg last:rounded-b-lg text-sm {{ $b['status']==='closed'?'opacity-50 cursor-not-allowed':'' }}"
          {{ $b['status']==='closed'?'disabled':'' }}>
          <div class="w-2 h-2 rounded-full {{ $b['status']==='open'?'bg-green-500':'bg-red-500' }}"></div>
          {{ $b['name'] }}
        </button>
        @endforeach
      </div>
    </div>

    <div class="flex gap-2">
      <button onclick="setMode('delivery')" id="desk-btn-delivery" class="flex-1 flex items-center justify-center gap-1 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs font-bold bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C] transition-all">
        🛵 Giao hàng
      </button>
      <button onclick="setMode('pickup')" id="desk-btn-pickup" class="flex-1 flex items-center justify-center gap-1 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs font-bold bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] transition-all">
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
  <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
    @php
    $navItems = [
      ['route'=>'client.home',        'icon'=>'🏠', 'label'=>'Trang chủ'],
      ['route'=>'client.menu',        'icon'=>'🍽️', 'label'=>'Thực đơn'],
      ['route'=>'client.cart',        'icon'=>'🛒', 'label'=>'Giỏ hàng', 'badge'=>2, 'auth'=>true],
      ['route'=>'client.profile',     'icon'=>'👤', 'label'=>'Tài khoản', 'auth'=>true],
      ['route'=>'client.group-order', 'icon'=>'👥', 'label'=>'Đặt nhóm',  'auth'=>true],
    ];
    @endphp
    @foreach($navItems as $item)
    @if(empty($item['auth']) || auth()->check())
    <a href="{{ route($item['route']) }}"
      class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all relative
             {{ request()->routeIs($item['route']) ? 'bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]' : 'text-gray-600 hover:bg-orange-50 hover:text-[#FF6B35]' }}">
      <span class="text-xl">{{ $item['icon'] }}</span>
      {{ $item['label'] }}
      @if(isset($item['badge']) && $item['badge'] > 0)
      <span class="ml-auto w-5 h-5 bg-[#FFD23F] border border-[#1C1C1C] rounded-full text-[10px] text-[#1C1C1C] font-black flex items-center justify-center">{{ $item['badge'] }}</span>
      @endif
    </a>
    @endif
    @endforeach

    @guest
    <div class="pt-2 border-t border-gray-100 space-y-1">
      <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm text-gray-600 hover:bg-orange-50 hover:text-[#FF6B35] transition-all">
        <span class="text-xl">🔐</span> Đăng nhập
      </a>
      <a href="{{ route('register') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm bg-[#FFD23F] text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all">
        <span class="text-xl">✨</span> Đăng ký
      </a>
    </div>
    @endguest
  </nav>

  {{-- Admin link --}}
  @auth
  @if(auth()->user()->role !== 'customer')
  <div class="p-4 border-t-2 border-[#1C1C1C]">
    <a href="{{ route('admin.kds') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm bg-[#1C1C1C] text-white shadow-[4px_4px_0px_#FF6B35] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
      <span class="text-xl">🍳</span> Admin Portal
    </a>
  </div>
  @endif
  @endauth

</aside>
