{{-- Mobile bottom nav --}}
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-[#1C1C1C] z-40">
  <div class="flex items-center justify-around px-2 py-2 max-w-lg mx-auto">
    @php
    $navItems = [
      ['route'=>'client.home',    'icon'=>'🏠', 'label'=>'Trang chủ'],
      ['route'=>'client.menu',    'icon'=>'🍽️', 'label'=>'Thực đơn'],
      ['route'=>'client.cart',    'icon'=>'🛒', 'label'=>'Giỏ hàng', 'badge'=>2, 'auth'=>true],
      ['route'=>'client.profile', 'icon'=>'👤', 'label'=>'Tài khoản'],
    ];
    @endphp
    @foreach($navItems as $item)
    @if(empty($item['auth']) || auth()->check())
    <a href="{{ route($item['route']) }}"
      class="flex flex-col items-center gap-0.5 px-4 py-1.5 rounded-xl transition-all relative
             {{ request()->routeIs($item['route']) ? 'bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]' : 'text-gray-500 hover:text-[#FF6B35]' }}">
      <span class="text-xl">{{ $item['icon'] }}</span>
      <span class="text-[10px] font-bold">{{ $item['label'] }}</span>
      @if(isset($item['badge']) && $item['badge'] > 0 && auth()->check())
      <span class="absolute -top-1 right-1 w-4 h-4 bg-[#FFD23F] border border-[#1C1C1C] rounded-full text-[9px] text-[#1C1C1C] font-black flex items-center justify-center">{{ $item['badge'] }}</span>
      @endif
    </a>
    @else
    <a href="{{ route('login') }}" class="flex flex-col items-center gap-0.5 px-4 py-1.5 rounded-xl text-gray-500 hover:text-[#FF6B35] transition-all">
      <span class="text-xl">{{ $item['icon'] }}</span>
      <span class="text-[10px] font-bold">{{ $item['label'] }}</span>
    </a>
    @endif
    @endforeach
  </div>
</nav>
