@extends('layouts.client')
@section('title', 'Thực đơn')

@push('styles')
<style>
  @keyframes shimmer { 0%{background-position:-800px 0} 100%{background-position:800px 0} }
  .skeleton { background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:800px 100%; animation:shimmer 1.5s infinite; border-radius:12px; }
</style>
@endpush

@section('content')

{{-- Skeleton --}}
<div id="skeleton-menu" class="p-4 lg:p-8 max-w-7xl mx-auto">
  <div class="flex gap-3 mb-5"><div class="skeleton flex-1 h-11"></div><div class="skeleton w-20 h-11"></div></div>
  <div class="flex gap-2 mb-5">@for($i=0;$i<5;$i++)<div class="skeleton h-9 w-20 flex-shrink-0"></div>@endfor</div>
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @for($i=0;$i<6;$i++)
    <div class="flex sm:flex-col gap-3 border-2 border-gray-100 rounded-2xl overflow-hidden p-3">
      <div class="skeleton w-28 h-28 sm:w-full sm:h-44 flex-shrink-0"></div>
      <div class="flex-1 space-y-2"><div class="skeleton h-4 w-3/4"></div><div class="skeleton h-3 w-full"></div><div class="skeleton h-8 w-full mt-2"></div></div>
    </div>
    @endfor
  </div>
</div>

<div id="main-menu" class="hidden">
<div class="p-4 lg:p-8 max-w-7xl mx-auto">

  {{-- Search + Filter --}}
  <div class="flex gap-3 mb-5">
    <form action="{{ route('client.menu') }}" method="GET" class="flex-1 relative">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm trong thực đơn..."
        class="w-full pl-9 pr-4 py-2.5 border-2 border-[#1C1C1C] rounded-xl bg-white shadow-[2px_2px_0px_#1C1C1C] text-sm outline-none focus:border-[#FF6B35] transition-all" />
    </form>
    <button onclick="toggleFilter()" class="px-4 py-2.5 border-2 border-[#1C1C1C] rounded-xl bg-[#FFD23F] shadow-[2px_2px_0px_#1C1C1C] font-bold text-sm flex items-center gap-2 hover:shadow-none transition-all">
      ⚙️ <span class="hidden sm:inline">Lọc</span>
    </button>
  </div>

  {{-- Filter panel --}}
  <div id="filter-panel" class="hidden mb-5 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4">
    <p class="text-xs font-black text-[#1C1C1C] mb-3 uppercase tracking-wide">Sắp xếp theo</p>
    <div class="flex flex-wrap gap-2">
      @foreach([['id'=>'popular','label'=>'Phổ biến nhất'],['id'=>'price_asc','label'=>'Giá tăng dần'],['id'=>'price_desc','label'=>'Giá giảm dần']] as $opt)
      <a href="{{ route('client.menu', array_merge(request()->all(), ['sort' => $opt['id']])) }}"
        class="text-xs font-bold px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] transition-all
               {{ request('sort','popular') === $opt['id'] ? 'bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]' : 'bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none' }}">
        {{ $opt['label'] }}
      </a>
      @endforeach
    </div>
  </div>

  {{-- Categories --}}
  <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-2 mb-5 lg:flex-wrap lg:overflow-visible">
    <a href="{{ route('client.menu', array_merge(request()->except('category'), ['category' => 'all'])) }}"
      class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs lg:text-sm font-bold transition-all
             {{ request('category','all') === 'all' ? 'bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]' : 'bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none' }}">
      🍽️ Tất cả
    </a>
    @foreach($categories as $cat)
    <a href="{{ route('client.menu', array_merge(request()->except('category'), ['category' => $cat->slug])) }}"
      class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs lg:text-sm font-bold transition-all
             {{ request('category') === $cat->slug ? 'bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]' : 'bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none' }}">
      {{ $cat->icon }} {{ $cat->name }}
    </a>
    @endforeach
  </div>

  <p class="text-xs text-gray-500 mb-4">{{ $menuItems->count() }} món</p>

  @if($menuItems->isEmpty())
  <div class="text-center py-20">
    <div class="text-6xl mb-4">🍜</div>
    <p class="font-black text-[#1C1C1C] text-xl">Không tìm thấy món nào</p>
    <a href="{{ route('client.menu') }}" class="mt-4 inline-block bg-[#FF6B35] text-white font-black px-5 py-2.5 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C]">Xem tất cả</a>
  </div>
  @endif

  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($menuItems as $item)
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all group">
      <a href="{{ route('client.product', $item->id) }}" class="flex sm:flex-col">
        <div class="relative w-28 sm:w-full flex-shrink-0 sm:flex-shrink overflow-hidden">
          <img src="{{ $item->image }}" alt="{{ $item->name }}"
            class="w-full h-28 sm:h-44 object-cover group-hover:scale-105 transition-transform duration-300" />
          @if($item->is_new)
          <div class="absolute top-2 left-2 bg-[#FFD23F] border border-[#1C1C1C] text-[#1C1C1C] text-[9px] font-black px-1.5 py-0.5 rounded-full">NEW</div>
          @endif
          @if($item->is_best_seller)
          <div class="absolute top-2 right-2 bg-[#FF6B35] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">🔥</div>
          @endif
        </div>
        <div class="flex-1 p-3">
          <div class="font-black text-[#1C1C1C] text-sm leading-tight">{{ $item->name }}</div>
          <div class="text-gray-500 text-xs mt-1 line-clamp-2">{{ $item->description }}</div>
          <div class="flex items-center gap-2 mt-2 text-[10px] text-gray-400">
            <span>{{ $item->category?->name }}</span>
            @if($item->calories)<span>· {{ $item->calories }} kcal</span>@endif
          </div>
        </div>
      </a>
      <div class="px-3 pb-3 flex items-center justify-between">
        <span class="font-black text-[#FF6B35] text-base">{{ number_format($item->base_price) }}đ</span>
        <div class="flex items-center gap-2">
          <button onclick="removeFromCart({{ $item->id }})" id="btn-minus-{{ $item->id }}"
            class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-white items-center justify-center shadow-[1px_1px_0px_#1C1C1C] font-black text-lg hidden">−</button>
          <span id="qty-{{ $item->id }}" class="font-black text-[#1C1C1C] text-sm min-w-[20px] text-center hidden">0</span>
          <button onclick="addToCart({{ $item->id }})"
            class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all text-lg">+</button>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Floating cart --}}
  <div id="cart-bar" class="fixed bottom-20 left-4 right-4 lg:bottom-6 lg:left-auto lg:right-8 lg:w-80 z-20 hidden">
    <a href="{{ route('client.cart') }}"
      class="w-full bg-[#FF6B35] text-white font-black py-4 rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] flex items-center justify-between px-5 hover:shadow-none transition-all block">
      <span class="bg-white/20 px-2 py-0.5 rounded-lg text-sm" id="cart-count">0 món</span>
      <span>Xem giỏ hàng →</span>
      <span id="cart-total">0đ</span>
    </a>
  </div>

</div>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', () => {
  document.getElementById('skeleton-menu').classList.add('hidden');
  document.getElementById('main-menu').classList.remove('hidden');
});

let cart = {};
const prices = @json($menuItems->pluck('base_price', 'id'));

function addToCart(id) {
  cart[id] = (cart[id] || 0) + 1;
  updateUI(id);
  fetch('/cart/add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ product_id: id, quantity: 1 })
  }).then(r => r.json()).then(d => {
    if (typeof showToast === 'function') showToast('Đã thêm vào giỏ hàng 🛒', 'success');
  });
}
function removeFromCart(id) {
  if (cart[id] > 0) cart[id]--;
  if (cart[id] === 0) delete cart[id];
  updateUI(id);
}
function updateUI(id) {
  const qty = cart[id] || 0;
  const qtyEl = document.getElementById('qty-' + id);
  const minusEl = document.getElementById('btn-minus-' + id);
  if (qtyEl) { qtyEl.textContent = qty; qtyEl.classList.toggle('hidden', qty === 0); }
  if (minusEl) minusEl.classList.toggle('hidden', qty === 0);
  const total = Object.entries(cart).reduce((s, [k, v]) => s + (prices[k] || 0) * v, 0);
  const count = Object.values(cart).reduce((a, b) => a + b, 0);
  document.getElementById('cart-count').textContent = count + ' món';
  document.getElementById('cart-total').textContent = total.toLocaleString('vi-VN') + 'đ';
  document.getElementById('cart-bar').classList.toggle('hidden', count === 0);
}
function toggleFilter() {
  document.getElementById('filter-panel').classList.toggle('hidden');
}
</script>
@endpush
