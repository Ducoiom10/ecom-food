@extends('layouts.client')
@section('title', 'Giỏ hàng')
@section('page_heading', 'Giỏ hàng')

@push('styles')
<style>
  @keyframes shimmer { 0%{background-position:-800px 0} 100%{background-position:800px 0} }
  .skeleton { background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:800px 100%; animation:shimmer 1.5s infinite; border-radius:12px; }
</style>
@endpush

@section('content')

{{-- Skeleton --}}
<div id="skeleton-cart" class="p-4 lg:p-8 max-w-7xl mx-auto">
  <div class="lg:grid lg:grid-cols-3 lg:gap-8">
    <div class="lg:col-span-2 space-y-4">
      <div class="skeleton h-12 w-full"></div>
      <div class="skeleton h-12 w-full"></div>
      @for($i=0;$i<2;$i++)
      <div class="flex gap-3"><div class="skeleton w-24 h-24 flex-shrink-0"></div><div class="flex-1 space-y-2"><div class="skeleton h-5 w-3/4"></div><div class="skeleton h-4 w-1/2"></div><div class="skeleton h-8 w-full"></div></div></div>
      @endfor
    </div>
    <div class="hidden lg:block space-y-4">
      <div class="skeleton h-32 w-full"></div>
      <div class="skeleton h-40 w-full"></div>
      <div class="skeleton h-28 w-full"></div>
      <div class="skeleton h-14 w-full"></div>
    </div>
  </div>
</div>

<div id="main-cart" class="hidden p-4 lg:p-8 max-w-7xl mx-auto">

  @if(session('success'))
  <div class="bg-green-50 border-2 border-green-400 rounded-xl px-4 py-3 mb-4 flex items-center gap-2 text-green-700 font-bold text-sm">
    ✅ {{ session('success') }}
  </div>
  @endif

  @if($errors->any())
  <div class="bg-red-50 border-2 border-red-400 rounded-xl px-4 py-3 mb-4 text-red-700 text-sm font-bold">
    ⚠️ {{ $errors->first() }}
  </div>
  @endif

  <div class="lg:grid lg:grid-cols-3 lg:gap-8">

    {{-- LEFT --}}
    <div class="lg:col-span-2 space-y-4">

      {{-- Free shipping progress --}}
      @php $freeShipTarget = 100000; $remaining = max(0, $freeShipTarget - $subtotal); $progress = min(100, ($subtotal / $freeShipTarget) * 100); @endphp
      @if($remaining > 0)
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xl">🛵</span>
          <p class="text-sm font-bold text-[#1C1C1C]">
            Mua thêm <span class="text-[#FF6B35] font-black">{{ number_format($remaining) }}đ</span>
            để được <span class="text-green-600 font-black">miễn phí vận chuyển!</span>
          </p>
        </div>
        <div class="h-3 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
          <div class="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full transition-all" style="width: {{ $progress }}%"></div>
        </div>
      </div>
      @endif

      {{-- Delivery mode --}}
      <div class="flex gap-3">
        <button onclick="setDelivery('delivery')" id="btn-delivery"
          class="flex-1 py-3 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C] transition-all">
          🛵 Giao hàng (+15.000đ)
        </button>
        <button onclick="setDelivery('pickup')" id="btn-pickup"
          class="flex-1 py-3 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] transition-all">
          🏪 Tự đến lấy (Free)
        </button>
      </div>

      {{-- Cart items --}}
      @if(empty($cart))
      <div class="text-center py-16 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C]">
        <div class="text-6xl mb-4">🛒</div>
        <p class="font-black text-[#1C1C1C] text-xl">Giỏ hàng trống</p>
        <p class="text-gray-500 text-sm mt-2">Hãy thêm món ăn vào giỏ nhé!</p>
        <a href="{{ route('client.menu') }}" class="mt-4 inline-block bg-[#FF6B35] text-white font-black px-5 py-2.5 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C]">
          Xem thực đơn →
        </a>
      </div>
      @else
      <div class="space-y-3" id="cart-items">
        @foreach($cart as $key => $item)
        <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden flex" id="cart-item-{{ $key }}">
          <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-24 lg:w-32 object-cover flex-shrink-0" />
          <div class="flex-1 p-3 lg:p-4 flex flex-col justify-between">
            <div>
              <div class="font-black text-[#1C1C1C] text-sm lg:text-base">{{ $item['name'] }}</div>
              @if(!empty($item['toppings']))
              <div class="text-xs text-gray-500 mt-0.5">+ {{ implode(', ', $item['toppings']) }}</div>
              @endif
              @if(!empty($item['note']))
              <div class="text-xs text-orange-500 mt-0.5">📝 {{ $item['note'] }}</div>
              @endif
              @if(!empty($item['size']))
              <div class="text-xs text-gray-400">Size: {{ $item['size'] }}</div>
              @endif
            </div>
            <div class="flex items-center justify-between mt-2">
              <span class="font-black text-[#FF6B35] text-base" id="item-price-{{ $key }}">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
              <div class="flex items-center gap-2">
                <button onclick="updateQty('{{ $key }}', {{ $item['quantity'] - 1 }}, {{ $item['price'] }})"
                  class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-white flex items-center justify-center font-black shadow-[1px_1px_0px_#1C1C1C]">−</button>
                <span class="font-black text-[#1C1C1C] text-sm w-6 text-center" id="qty-{{ $key }}">{{ $item['quantity'] }}</span>
                <button onclick="updateQty('{{ $key }}', {{ $item['quantity'] + 1 }}, {{ $item['price'] }})"
                  class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C]">+</button>
                <button onclick="removeItem('{{ $key }}')"
                  class="w-8 h-8 rounded-lg border-2 border-red-200 bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors">🗑</button>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      {{-- Upsell --}}
      @if($upsell)
      <div class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 flex items-center gap-3" id="upsell-box">
        <img src="{{ $upsell->image }}" alt="{{ $upsell->name }}" class="w-14 h-14 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
        <div class="flex-1">
          <p class="font-black text-[#1C1C1C] text-sm">Thêm {{ $upsell->name }} chỉ {{ number_format($upsell->base_price) }}đ?</p>
          <p class="text-xs text-[#1C1C1C]/60">Gợi ý cho bạn 😋</p>
        </div>
        <div class="flex flex-col gap-1">
          <a href="{{ route('client.product', $upsell->id) }}" class="bg-[#FF6B35] text-white text-xs font-black px-3 py-1.5 rounded-lg border border-[#1C1C1C] text-center">+ Thêm</a>
          <button onclick="document.getElementById('upsell-box').remove()" class="text-xs text-gray-600">Bỏ qua</button>
        </div>
      </div>
      @endif

      {{-- Delivery info --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 space-y-3" id="delivery-info">
        <div class="flex items-center gap-2">
          <span>📍</span><span class="font-black text-[#1C1C1C] text-sm">Địa chỉ giao hàng</span>
        </div>
        <input type="text" id="delivery-address" placeholder="Nhập địa chỉ giao hàng..."
          value="{{ auth()->user()->addresses->where('is_default', true)->first()?->address ?? '' }}"
          class="w-full border-2 border-[#1C1C1C] rounded-xl px-3 py-2.5 text-sm outline-none focus:border-[#FF6B35]" />
        <div class="flex items-center gap-2 text-sm">
          <span>🕐</span><span class="font-black text-[#1C1C1C]">Thời gian giao: <span class="text-gray-500 font-medium">~20-25 phút</span></span>
        </div>
      </div>
      @endif

    </div>

    {{-- RIGHT: Summary (sticky) --}}
    <div class="mt-6 lg:mt-0 space-y-4 lg:sticky lg:top-24 lg:self-start">

      {{-- Voucher --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <button onclick="toggleVouchers()" class="w-full flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span>🏷️</span>
            <span class="font-black text-[#1C1C1C] text-sm" id="voucher-label">
              {{ session('applied_voucher') ? 'Đã dùng: ' . session('applied_voucher')['code'] : 'Chọn voucher' }}
            </span>
          </div>
          @if(session('applied_voucher'))
          <span class="text-green-600 font-black text-sm">-{{ number_format(session('applied_voucher')['discount']) }}đ</span>
          @else
          <span class="text-gray-400">›</span>
          @endif
        </button>
        <div id="voucher-list" class="mt-3 space-y-2 hidden">
          @foreach($vouchers as $v)
          <div onclick="applyVoucher('{{ $v->code }}')"
            class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#FF6B35]/30 hover:border-[#FF6B35] hover:bg-orange-50 cursor-pointer transition-all">
            <div class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></div>
            <div class="flex-1">
              <div class="font-black text-[#1C1C1C] text-sm">{{ $v->code }}</div>
              <div class="text-xs text-gray-500">
                @if($v->type === 'flat') Giảm {{ number_format($v->value) }}đ
                @elseif($v->type === 'percent') Giảm {{ $v->value }}%
                @else Free ship @endif
                · Đơn từ {{ number_format($v->min_order) }}đ
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Payment --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <p class="font-black text-[#1C1C1C] text-sm mb-3">💳 Thanh toán</p>
        <div class="grid grid-cols-2 gap-2">
          @foreach([['id'=>'momo','label'=>'MoMo','emoji'=>'💜'],['id'=>'cod','label'=>'Tiền mặt','emoji'=>'💵'],['id'=>'zalopay','label'=>'ZaloPay','emoji'=>'🔵'],['id'=>'bank','label'=>'Chuyển khoản','emoji'=>'🏦']] as $pm)
          <button onclick="selectPayment('{{ $pm['id'] }}')" id="pm-{{ $pm['id'] }}"
            class="flex items-center gap-2 py-2.5 px-3 rounded-xl border-2 text-sm font-bold transition-all {{ $pm['id']==='momo' ? 'border-[#FF6B35] bg-orange-50 text-[#FF6B35]' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
            {{ $pm['emoji'] }} {{ $pm['label'] }}
          </button>
          @endforeach
        </div>
      </div>

      {{-- Bill summary --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <p class="font-black text-[#1C1C1C] text-sm mb-3">Tóm tắt đơn hàng</p>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between text-gray-600">
            <span>Tạm tính ({{ count($cart) }} món)</span>
            <span id="subtotal-display">{{ number_format($subtotal) }}đ</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Phí vận chuyển</span>
            <span id="shipping-fee">15.000đ</span>
          </div>
          @if(session('applied_voucher'))
          <div class="flex justify-between text-green-600">
            <span>Voucher ({{ session('applied_voucher')['code'] }})</span>
            <span>-{{ number_format(session('applied_voucher')['discount']) }}đ</span>
          </div>
          @endif
          <div class="border-t-2 border-[#1C1C1C] pt-2 flex justify-between font-black text-[#1C1C1C] text-base">
            <span>Tổng cộng</span>
            <span class="text-[#FF6B35]" id="total-display">
              {{ number_format($subtotal + 15000 - (session('applied_voucher')['discount'] ?? 0)) }}đ
            </span>
          </div>
        </div>
      </div>

      {{-- Checkout --}}
      @if(!empty($cart))
      <form action="{{ route('client.checkout.post') }}" method="POST" id="checkout-form">
        @csrf
        <input type="hidden" name="payment_method" id="payment-input" value="momo" />
        <input type="hidden" name="delivery_mode" id="delivery-mode-input" value="delivery" />
        <input type="hidden" name="branch_id" value="1" />
        <input type="hidden" name="delivery_address" id="delivery-address-input" value="" />
        <button type="submit" id="checkout-btn"
          class="w-full bg-[#FF6B35] text-white font-black py-4 rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all flex items-center justify-center gap-2 text-lg">
          ⚡ Đặt hàng ngay · <span id="btn-total">{{ number_format($subtotal + 15000 - (session('applied_voucher')['discount'] ?? 0)) }}đ</span>
        </button>
      </form>
      <p class="text-center text-xs text-gray-400 mt-2">Bằng cách đặt hàng, bạn đồng ý với Điều khoản dịch vụ</p>
      @endif

    </div>
  </div>
</div>{{-- end main-cart --}}

@endsection

@push('scripts')
<script>
window.addEventListener('load', () => {
  document.getElementById('skeleton-cart').classList.add('hidden');
  document.getElementById('main-cart').classList.remove('hidden');
});

let deliveryMode = 'delivery';
const subtotal = {{ $subtotal }};
const appliedDiscount = {{ session('applied_voucher') ? session('applied_voucher')['discount'] : 0 }};

function setDelivery(mode) {
  deliveryMode = mode;
  document.getElementById('delivery-mode-input').value = mode;
  const d = document.getElementById('btn-delivery');
  const p = document.getElementById('btn-pickup');
  const info = document.getElementById('delivery-info');
  if (mode === 'delivery') {
    d.className = d.className.replace('bg-white text-[#1C1C1C]','bg-[#FF6B35] text-white');
    p.className = p.className.replace('bg-[#1C1C1C] text-white','bg-white text-[#1C1C1C]');
    document.getElementById('shipping-fee').textContent = '15.000đ';
    if (info) info.classList.remove('hidden');
  } else {
    p.className = p.className.replace('bg-white text-[#1C1C1C]','bg-[#1C1C1C] text-white');
    d.className = d.className.replace('bg-[#FF6B35] text-white','bg-white text-[#1C1C1C]');
    document.getElementById('shipping-fee').textContent = 'Miễn phí';
    if (info) info.classList.add('hidden');
  }
  recalcTotal();
}

function recalcTotal() {
  const ship = deliveryMode === 'delivery' ? 15000 : 0;
  const total = subtotal + ship - appliedDiscount;
  document.getElementById('total-display').textContent = total.toLocaleString('vi-VN') + 'đ';
  const btnTotal = document.getElementById('btn-total');
  if (btnTotal) btnTotal.textContent = total.toLocaleString('vi-VN') + 'đ';
}

function applyVoucher(code) {
  fetch('{{ route('client.cart.voucher') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ code })
  }).then(r => r.json()).then(data => {
    if (data.ok) {
      document.getElementById('voucher-label').textContent = 'Đã dùng: ' + data.code;
      document.getElementById('voucher-list').classList.add('hidden');
      location.reload();
    } else {
      alert(data.message);
    }
  });
}

function toggleVouchers() { document.getElementById('voucher-list').classList.toggle('hidden'); }

function selectPayment(id) {
  document.getElementById('payment-input').value = id;
  ['momo','cod','zalopay','bank'].forEach(p => {
    const el = document.getElementById('pm-' + p);
    if (p === id) { el.classList.add('border-[#FF6B35]','bg-orange-50','text-[#FF6B35]'); el.classList.remove('border-gray-200','text-gray-600'); }
    else { el.classList.remove('border-[#FF6B35]','bg-orange-50','text-[#FF6B35]'); el.classList.add('border-gray-200','text-gray-600'); }
  });
}

function updateQty(key, newQty, price) {
  if (newQty < 1) { removeItem(key); return; }
  fetch(`/cart/${key}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ quantity: newQty })
  }).then(() => {
    document.getElementById('qty-' + key).textContent = newQty;
    document.getElementById('item-price-' + key).textContent = (price * newQty).toLocaleString('vi-VN') + 'đ';
  });
}

function removeItem(key) {
  fetch(`/cart/${key}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  }).then(() => {
    document.getElementById('cart-item-' + key)?.remove();
  });
}

// Sync delivery address to form
document.getElementById('checkout-form')?.addEventListener('submit', function() {
  const addr = document.getElementById('delivery-address')?.value;
  document.getElementById('delivery-address-input').value = addr || '';
});
</script>
@endpush
