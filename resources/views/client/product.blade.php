@extends('layouts.client')
@section('title', $product['name'] ?? 'Chi tiết món')
@section('page_heading', $product['name'] ?? 'Chi tiết món')

@push('styles')
<style>
  /* Skeleton shimmer */
  @keyframes shimmer { 0%{background-position:-800px 0} 100%{background-position:800px 0} }
  .skeleton { background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:800px 100%; animation:shimmer 1.5s infinite; border-radius:8px; }

  /* Tooltip cho admin sidebar ở lg breakpoint */
  .sidebar-tooltip { position:relative; }
  .sidebar-tooltip:hover .tooltip-text { opacity:1; pointer-events:auto; }
  .tooltip-text { opacity:0; pointer-events:none; position:absolute; left:calc(100% + 8px); top:50%; transform:translateY(-50%); background:#1C1C1C; color:white; font-size:12px; font-weight:700; padding:4px 10px; border-radius:8px; white-space:nowrap; transition:opacity .15s; z-index:99; }
  .tooltip-text::before { content:''; position:absolute; right:100%; top:50%; transform:translateY(-50%); border:5px solid transparent; border-right-color:#1C1C1C; }
</style>
@endpush

@section('content')

{{-- ===== SKELETON LOADER (hiện khi load) ===== --}}
<div id="skeleton-screen" class="p-4 lg:p-8 max-w-7xl mx-auto">
  <div class="lg:grid lg:grid-cols-2 lg:gap-10">
    <div class="skeleton h-72 lg:h-[480px] w-full mb-4 lg:mb-0"></div>
    <div class="space-y-4 px-0 lg:px-0">
      <div class="skeleton h-8 w-3/4"></div>
      <div class="skeleton h-4 w-1/2"></div>
      <div class="skeleton h-16 w-full"></div>
      <div class="skeleton h-12 w-full"></div>
      <div class="skeleton h-12 w-full"></div>
      <div class="skeleton h-12 w-full"></div>
    </div>
  </div>
</div>

{{-- ===== MAIN CONTENT (ẩn khi load) ===== --}}
<div id="main-content" class="hidden p-4 lg:p-8 max-w-7xl mx-auto pb-32 lg:pb-8">

  {{-- Back --}}
  <a href="{{ route('client.menu') }}"
    class="inline-flex items-center gap-2 text-sm font-bold text-[#1C1C1C] mb-4 hover:text-[#FF6B35] transition-colors">
    ← Quay lại thực đơn
  </a>

  <div class="lg:grid lg:grid-cols-2 lg:gap-10 lg:items-start">

    {{-- ===== LEFT: Image ===== --}}
    <div class="lg:sticky lg:top-24">
      <div class="relative overflow-hidden rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] aspect-[4/3]">
        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
          class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" />
        @if($product['isNew'] ?? false)
        <div class="absolute top-3 left-3 bg-[#FFD23F] border border-[#1C1C1C] text-[#1C1C1C] text-xs font-black px-2 py-1 rounded-full shadow-[1px_1px_0px_#1C1C1C]">✨ MỚI</div>
        @endif
        @if($product['isBestSeller'] ?? false)
        <div class="absolute top-3 right-3 bg-[#FF6B35] text-white text-xs font-black px-2 py-1 rounded-full shadow-[1px_1px_0px_#1C1C1C]">🔥 BÁN CHẠY</div>
        @endif
      </div>

      {{-- Stats bar - desktop only --}}
      <div class="hidden lg:grid grid-cols-3 gap-3 mt-4">
        @foreach([
          ['icon'=>'⭐','value'=>$product['rating'],'label'=>'Đánh giá'],
          ['icon'=>'🛒','value'=>$product['sold'].'+','label'=>'Đã bán'],
          ['icon'=>'📍','value'=>$product['distance'],'label'=>'Khoảng cách'],
        ] as $stat)
        <div class="bg-white border-2 border-[#1C1C1C] rounded-xl p-3 text-center shadow-[2px_2px_0px_#1C1C1C]">
          <div class="text-xl mb-1">{{ $stat['icon'] }}</div>
          <div class="font-black text-[#1C1C1C] text-sm">{{ $stat['value'] }}</div>
          <div class="text-xs text-gray-400">{{ $stat['label'] }}</div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- ===== RIGHT: Info + Options ===== --}}
    <div class="mt-4 lg:mt-0 space-y-5">

      {{-- Name + meta --}}
      <div>
        <h1 class="font-black text-[#1C1C1C] text-2xl lg:text-3xl leading-tight">{{ $product['name'] }}</h1>
        <div class="flex items-center gap-3 mt-2 text-sm text-gray-500 lg:hidden">
          <span>⭐ {{ $product['rating'] }}</span>
          <span>· {{ $product['sold'] }}+ bán</span>
          <span>· {{ $product['distance'] }}</span>
        </div>
        <p class="text-gray-600 text-sm lg:text-base mt-3 leading-relaxed">{{ $product['description'] }}</p>
      </div>

      {{-- Price + delivery --}}
      <div class="flex items-center justify-between bg-[#FAFAF8] border-2 border-[#1C1C1C] rounded-xl px-4 py-3 shadow-[2px_2px_0px_#1C1C1C]">
        <div>
          <div class="text-xs text-gray-400 font-medium">Giá từ</div>
          <div class="font-black text-[#FF6B35] text-2xl lg:text-3xl">{{ number_format($product['price']) }}đ</div>
        </div>
        <div class="text-right">
          <div class="text-xs text-gray-400 font-medium">Dự kiến giao</div>
          <div class="font-black text-[#1C1C1C] text-base flex items-center gap-1 justify-end">
            <span class="text-blue-500">🕐</span> ~15 phút
          </div>
        </div>
      </div>

      {{-- Nutrition collapsible --}}
      <details class="bg-green-50 border-2 border-green-200 rounded-xl overflow-hidden group">
        <summary class="px-4 py-3 font-bold text-sm text-green-800 cursor-pointer flex items-center justify-between list-none">
          <span>🥗 Thông tin dinh dưỡng</span>
          <span class="text-green-500 group-open:rotate-180 transition-transform">▾</span>
        </summary>
        <div class="px-4 pb-4 space-y-2">
          @foreach([
            ['label'=>'Calories','value'=>($product['calories'] ?? 0).' kcal'],
            ['label'=>'Protein','value'=>'~18g'],
            ['label'=>'Carbs','value'=>'~52g'],
            ['label'=>'Fat','value'=>'~12g'],
          ] as $n)
          <div class="flex justify-between text-sm py-1.5 border-b border-green-100 last:border-0">
            <span class="text-gray-600">{{ $n['label'] }}</span>
            <span class="font-bold text-[#1C1C1C]">{{ $n['value'] }}</span>
          </div>
          @endforeach
        </div>
      </details>

      {{-- Size --}}
      @if(!empty($product['sizes']))
      <div>
        <h3 class="font-black text-[#1C1C1C] text-base mb-3">Chọn size</h3>
        <div class="flex gap-2">
          @foreach($product['sizes'] as $size)
          <label class="flex-1 cursor-pointer">
            <input type="radio" name="size" value="{{ $size['id'] }}" class="hidden peer" {{ $loop->first ? 'checked' : '' }}
              onchange="updatePrice({{ $size['price'] }})" />
            <div class="peer-checked:bg-[#FF6B35] peer-checked:text-white peer-checked:border-[#FF6B35] peer-checked:shadow-none
              border-2 border-[#1C1C1C] rounded-xl py-2.5 text-center text-sm font-bold cursor-pointer transition-all shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px]">
              <div>{{ $size['name'] }}</div>
              @if($size['price'] > 0)
              <div class="text-xs opacity-80">+{{ number_format($size['price']) }}đ</div>
              @else
              <div class="text-xs opacity-60">Mặc định</div>
              @endif
            </div>
          </label>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Toppings --}}
      @if(!empty($product['toppings']))
      <div>
        <h3 class="font-black text-[#1C1C1C] text-base mb-3">Topping thêm <span class="text-gray-400 font-medium text-sm">(tuỳ chọn)</span></h3>
        <div class="space-y-2">
          @foreach($product['toppings'] as $topping)
          <label class="flex items-center justify-between bg-white border-2 border-[#1C1C1C] rounded-xl px-4 py-3 cursor-pointer hover:bg-orange-50 hover:border-[#FF6B35] transition-all shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none group">
            <div class="flex items-center gap-3">
              <div class="w-5 h-5 border-2 border-[#1C1C1C] rounded-md flex items-center justify-center group-has-[:checked]:bg-[#FF6B35] group-has-[:checked]:border-[#FF6B35] transition-all flex-shrink-0">
                <input type="checkbox" name="toppings[]" value="{{ $topping['id'] }}"
                  class="opacity-0 absolute" onchange="toggleTopping({{ $topping['price'] }}, this.checked)" />
                <svg class="w-3 h-3 text-white hidden group-has-[:checked]:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <span class="font-bold text-sm text-[#1C1C1C]">{{ $topping['name'] }}</span>
            </div>
            <span class="text-[#FF6B35] font-black text-sm">+{{ number_format($topping['price']) }}đ</span>
          </label>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Note --}}
      <div>
        <h3 class="font-black text-[#1C1C1C] text-base mb-2">Ghi chú cho bếp</h3>
        <textarea id="pdp-note" placeholder="Ví dụ: ít hành, không cay, thêm sốt..." rows="2"
          class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] focus:shadow-[2px_2px_0px_#FF6B35] resize-none transition-all"></textarea>
      </div>

      {{-- CTA Desktop (inline, không fixed) --}}
      <div class="hidden lg:block bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4">
        <div class="flex items-center justify-between mb-4">
          <div>
            <div class="text-xs text-gray-400">Tổng cộng</div>
            <div class="font-black text-[#FF6B35] text-2xl" id="pdp-total-desk">{{ number_format($product['price']) }}đ</div>
          </div>
          <div class="flex items-center gap-2 border-2 border-[#1C1C1C] rounded-xl overflow-hidden shadow-[2px_2px_0px_#1C1C1C]">
            <button onclick="changeQty(-1)" class="w-10 h-10 flex items-center justify-center font-black text-xl hover:bg-gray-100 transition-colors">−</button>
            <span class="font-black text-[#1C1C1C] text-lg min-w-[32px] text-center" id="pdp-qty-desk">1</span>
            <button onclick="changeQty(1)" class="w-10 h-10 flex items-center justify-center font-black text-xl bg-[#FF6B35] text-white hover:bg-[#e55a25] transition-colors">+</button>
          </div>
        </div>
        <button onclick="addToCartPDP()"
          class="w-full bg-[#FF6B35] text-white font-black py-4 rounded-xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all text-base flex items-center justify-center gap-2">
          🛒 Thêm vào giỏ hàng
        </button>
        <a href="{{ route('client.group-order') }}"
          class="mt-2 w-full border-2 border-[#1C1C1C] rounded-xl py-3 font-bold text-sm text-[#1C1C1C] flex items-center justify-center gap-2 hover:bg-[#FFD23F] transition-colors shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none block text-center">
          👥 Thêm vào đơn nhóm
        </a>
      </div>

      {{-- Related products --}}
      <div class="hidden lg:block">
        <h3 class="font-black text-[#1C1C1C] text-base mb-3">Có thể bạn thích</h3>
        <div class="grid grid-cols-2 gap-3">
          @foreach(array_slice(\App\Data\MockData::menuItems(), 0, 2) as $related)
          @if($related['id'] !== $product['id'])
          <a href="{{ route('client.product', $related['id']) }}"
            class="flex gap-2 bg-white border-2 border-[#1C1C1C] rounded-xl p-2 hover:bg-orange-50 hover:border-[#FF6B35] transition-all shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none">
            <img src="{{ $related['image'] }}" alt="{{ $related['name'] }}" class="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" />
            <div class="flex-1 min-w-0">
              <div class="font-bold text-xs text-[#1C1C1C] line-clamp-2 leading-tight">{{ $related['name'] }}</div>
              <div class="font-black text-[#FF6B35] text-sm mt-1">{{ number_format($related['price']) }}đ</div>
            </div>
          </a>
          @endif
          @endforeach
        </div>
      </div>

    </div>{{-- end right --}}
  </div>
</div>

{{-- ===== MOBILE STICKY CTA ===== --}}
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t-2 border-[#1C1C1C] px-4 py-3 z-40 shadow-[0_-4px_0px_#1C1C1C]">
  <div class="flex items-center justify-between mb-2">
    <div>
      <div class="text-xs text-gray-400">Tổng cộng</div>
      <div class="font-black text-[#FF6B35] text-xl" id="pdp-total-mob">{{ number_format($product['price']) }}đ</div>
    </div>
    <div class="flex items-center gap-1 border-2 border-[#1C1C1C] rounded-xl overflow-hidden shadow-[2px_2px_0px_#1C1C1C]">
      <button onclick="changeQty(-1)" class="w-9 h-9 flex items-center justify-center font-black text-lg hover:bg-gray-100">−</button>
      <span class="font-black text-[#1C1C1C] min-w-[28px] text-center" id="pdp-qty-mob">1</span>
      <button onclick="changeQty(1)" class="w-9 h-9 flex items-center justify-center font-black text-lg bg-[#FF6B35] text-white">+</button>
    </div>
  </div>
  <button onclick="addToCartPDP()"
    class="w-full bg-[#FF6B35] text-white font-black py-3.5 rounded-xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all flex items-center justify-center gap-2">
    🛒 Thêm vào giỏ hàng
  </button>
</div>

{{-- Toast notification --}}
<div id="toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 hidden">
  <div class="bg-[#1C1C1C] text-white font-bold text-sm px-5 py-3 rounded-xl shadow-[4px_4px_0px_#FF6B35] flex items-center gap-2">
    ✅ Đã thêm vào giỏ hàng!
  </div>
</div>

@endsection

@push('scripts')
<script>
const basePrice = {{ $product['price'] }};
let qty = 1, sizeExtra = 0, toppingExtra = 0;

// Show content after load
window.addEventListener('load', () => {
  document.getElementById('skeleton-screen').classList.add('hidden');
  document.getElementById('main-content').classList.remove('hidden');
});

function updatePrice(extra) {
  sizeExtra = extra;
  recalc();
}

function toggleTopping(price, checked) {
  toppingExtra += checked ? price : -price;
  recalc();
}

function recalc() {
  const total = (basePrice + sizeExtra + toppingExtra) * qty;
  const fmt = total.toLocaleString('vi-VN') + 'đ';
  document.getElementById('pdp-total-desk').textContent = fmt;
  document.getElementById('pdp-total-mob').textContent = fmt;
}

function changeQty(delta) {
  qty = Math.max(1, qty + delta);
  document.getElementById('pdp-qty-desk').textContent = qty;
  document.getElementById('pdp-qty-mob').textContent = qty;
  recalc();
}

function addToCartPDP() {
  const btn = document.querySelectorAll('[onclick="addToCartPDP()"]');
  btn.forEach(b => { b.disabled = true; b.textContent = '⏳ Đang thêm...'; });

  fetch('/cart/add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({
      id: '{{ $product['id'] }}',
      quantity: qty,
      note: document.getElementById('pdp-note')?.value || ''
    })
  }).then(() => {
    // Show toast
    const toast = document.getElementById('toast');
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 2500);
    btn.forEach(b => { b.disabled = false; b.innerHTML = '🛒 Thêm vào giỏ hàng'; });
  });
}
</script>
@endpush
