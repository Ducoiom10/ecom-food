{{-- Mobile header cho client layout --}}
<header class="sticky top-0 z-40 bg-[#FAFAF8] border-b-2 border-[#1C1C1C] px-4 pt-3 pb-3">
  <div class="flex items-center justify-between mb-3">
    <a href="{{ route('client.home') }}" class="flex items-center gap-2">
      <div class="w-8 h-8 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-lg flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C]">
        <span class="text-white text-xs">🍜</span>
      </div>
      <span class="font-black text-[#1C1C1C] tracking-tight">Ba Anh Em</span>
    </a>

    <div class="relative" id="mob-branch-wrap">
      <button onclick="toggleMobBranch(event)" class="flex items-center gap-1 text-xs border-2 border-[#1C1C1C] rounded-lg px-2 py-1 bg-white shadow-[2px_2px_0px_#1C1C1C]">
        <span>📍</span>
        <span class="max-w-[90px] truncate font-medium" id="mob-branch-label">Chi nhánh Quận 1</span>
        <span>▾</span>
      </button>
      <div id="mob-branch-menu" class="hidden absolute right-0 top-full mt-1 w-52 bg-white border-2 border-[#1C1C1C] rounded-xl shadow-[4px_4px_0px_#1C1C1C] z-50">
        @foreach([['name'=>'Chi nhánh Quận 1','status'=>'open'],['name'=>'Chi nhánh Quận 3','status'=>'open'],['name'=>'Chi nhánh Bình Thạnh','status'=>'open'],['name'=>'Chi nhánh Gò Vấp','status'=>'closed']] as $b)
        <button onclick="selectBranch('mob','{{ $b['name'] }}',{{ $b['status']==='closed'?'true':'false' }})"
          class="w-full text-left px-3 py-2 flex items-center gap-2 hover:bg-orange-50 first:rounded-t-lg last:rounded-b-lg text-sm {{ $b['status']==='closed'?'opacity-50 cursor-not-allowed':'' }}"
          {{ $b['status']==='closed'?'disabled':'' }}>
          <div class="w-2 h-2 rounded-full {{ $b['status']==='open'?'bg-green-500':'bg-red-500' }}"></div>
          {{ $b['name'] }}
        </button>
        @endforeach
      </div>
    </div>

    @auth
    <a href="{{ route('client.profile') }}" class="relative w-8 h-8 border-2 border-[#1C1C1C] rounded-lg bg-[#FF6B35] flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] text-white font-black text-xs">
      {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </a>
    @else
    <a href="{{ route('login') }}" class="text-xs font-black text-[#FF6B35] border-2 border-[#FF6B35] px-2 py-1 rounded-lg">Đăng nhập</a>
    @endauth
  </div>

  <div class="flex gap-2 mb-3">
    <button onclick="setMode('delivery')" id="mob-btn-delivery" class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C] transition-all">
      🛵 Giao hàng
    </button>
    <button onclick="setMode('pickup')" id="mob-btn-pickup" class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] transition-all">
      🏪 Tự đến lấy
    </button>
  </div>

  <form action="{{ route('client.menu') }}" method="GET" class="relative">
    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
    <input type="text" name="search" placeholder="Tìm món ăn, đồ uống..."
      class="w-full pl-9 pr-4 py-2.5 border-2 border-[#1C1C1C] rounded-xl bg-white shadow-[2px_2px_0px_#1C1C1C] text-sm outline-none focus:border-[#FF6B35] transition-all" />
  </form>
</header>
