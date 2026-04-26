@extends('layouts.client')
@section('title', 'Giỏ hàng')

@push('styles')
<style>
  @keyframes shimmer { 0%{background-position:-800px 0} 100%{background-position:800px 0} }
  .skeleton { background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:800px 100%; animation:shimmer 1.5s infinite; border-radius:12px; }
</style>
@endpush

@section('content')
<div id="main-cart" class="p-4 lg:p-8 max-w-7xl mx-auto">

  @if(session('success'))
  <div class="bg-green-50 border-2 border-green-400 rounded-xl px-4 py-3 mb-4 text-green-700 font-bold">
    ✅ {{ session('success') }}
  </div>
  @endif

  @if($errors->any())
  <div class="bg-red-50 border-2 border-red-400 rounded-xl px-4 py-3 mb-4 text-red-700 font-bold">
    ⚠️ {{ $errors->first() }}
  </div>
  @endif

  <h1 class="font-black text-[#1C1C1C] text-xl lg:text-2xl mb-6 lg:hidden">Giỏ hàng 🛒</h1>

  {{-- Free shipping progress --}}
  @php $freeShipTarget = 100000; $remaining = max(0, $freeShipTarget - ($subtotal ?? 0)); $pct = min(100, (($subtotal ?? 0) / $freeShipTarget) * 100); @endphp
  @if($remaining > 0)
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 mb-4">
    <div class="flex items-center gap-2 mb-2">
      <span class="text-lg">🚵</span>
      <p class="text-xs font-bold text-[#1C1C1C]">
        Mua thêm <span class="text-[#FF6B35] font-black">{{ number_format($remaining) }}đ</span> để được <span class="text-green-600 font-black">miễn phí vận chuyển!</span>
      </p>
    </div>
    <div class="relative h-3 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
      <div class="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full transition-all" style="width: {{ $pct }}%"></div>
      <span class="absolute text-base" style="left: {{ $pct }}%; top: 50%; transform: translate(-50%,-50%)">🚵</span>
    </div>
  </div>
  @else
  <div class="bg-green-50 border-2 border-green-300 rounded-2xl p-3 mb-4 flex items-center gap-2">
    <span class="text-lg">🎉</span>
    <p class="text-xs font-bold text-green-700">Được miễn phí vận chuyển!</p>
  </div>
  @endif

  <div class="lg:grid lg:grid-cols-3 lg:gap-8">

    {{-- LEFT: Cart items --}}
    <div class="lg:col-span-2 space-y-4">

      {{-- Delivery mode --}}
      <div class="flex gap-3">
        <button type="button" onclick="setDelivery('delivery')" id="btn-delivery"
          class="flex-1 py-3 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C] transition-all">
          🛵 Giao hàng (+15.000đ)
        </button>
        <button type="button" onclick="setDelivery('pickup')" id="btn-pickup"
          class="flex-1 py-3 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] transition-all">
          🏪 Tự đến lấy (Free)
        </button>
      </div>

      {{-- Cart items --}}
      <div class="space-y-3" id="cart-items">
        @forelse($cart ?? [] as $item)
        <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden flex" id="cart-item-{{ $item['id'] }}">
          <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-24 lg:w-32 object-cover flex-shrink-0" />
          <div class="flex-1 p-3 lg:p-4 flex flex-col justify-between">
            <div>
              <div class="font-black text-[#1C1C1C] text-sm lg:text-base">{{ $item['name'] }}</div>
              @if(!empty($item['toppings']))
              <div class="text-xs text-gray-500 mt-0.5">+ {{ implode(', ', $item['toppings']) }}</div>
              @endif
              @if(!empty($item['note']))
              <div class="text-xs text-orange-500 mt-0.5">Ghi chú: {{ $item['note'] }}</div>
              @endif
              @if($item['size'])
              <div class="text-xs text-gray-400">Size: {{ $item['size'] }}</div>
              @endif
            </div>
            <div class="flex items-center justify-between mt-2">
              <span class="font-black text-[#FF6B35] text-base" id="item-price-{{ $item['id'] }}" data-price="{{ $item['price'] }}">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
              <div class="flex items-center gap-2">
                <button type="button" onclick="updateQty('{{ $item['id'] }}', -1, {{ $item['price'] }})" class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-white flex items-center justify-center font-black shadow-[1px_1px_0px_#1C1C1C]">−</button>
                <span class="font-black text-[#1C1C1C] text-sm w-6 text-center" id="qty-{{ $item['id'] }}">{{ $item['quantity'] }}</span>
                <button type="button" onclick="updateQty('{{ $item['id'] }}', 1, {{ $item['price'] }})" class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C]">+</button>
                <form method="POST" action="{{ route('client.cart.remove', $item['id']) }}" class="inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="w-8 h-8 rounded-lg border-2 border-red-200 bg-red-50 text-red-500 flex items-center justify-center">🗑</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-400 bg-white border-2 border-dashed border-gray-200 rounded-2xl">
          <div class="text-5xl mb-3">🛒</div>
          <p class="font-bold">Giỏ hàng trống</p>
          <a href="{{ route('client.menu') }}" class="mt-3 inline-block text-[#FF6B35] font-black hover:underline">Xem thực đơn →</a>
        </div>
        @endforelse
      </div>

      {{-- Upsell widget --}}
      @if(!empty($upsell))
      <div id="upsell-widget" class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 flex items-center gap-3">
        <img src="{{ $upsell->image }}" alt="{{ $upsell->name }}" class="w-12 h-12 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
        <div class="flex-1">
          <p class="font-black text-[#1C1C1C] text-xs">Thêm {{ $upsell->name }} chỉ {{ number_format($upsell->base_price) }}đ?</p>
          <p class="text-[10px] text-[#1C1C1C]/60">Perfect combo với đơn của bạn 😋</p>
        </div>
        <div class="flex flex-col gap-1">
          <button onclick="addUpsell({{ $upsell->id }})" class="bg-[#FF6B35] text-white text-[10px] font-black px-2 py-1 rounded-lg border border-[#1C1C1C]">+ Thêm</button>
          <button onclick="document.getElementById('upsell-widget').remove()" class="text-[10px] text-gray-500">Bỏ qua</button>
        </div>
      </div>
      @endif

      {{-- Delivery address --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 space-y-3" id="delivery-info">
        <div class="flex items-center gap-2">
          <span>📍</span><span class="font-black text-[#1C1C1C] text-sm">Địa chỉ giao hàng</span>
        </div>
        <input type="text" id="delivery-address-input" placeholder="Nhập địa chỉ giao hàng..."
          class="w-full border-2 border-[#1C1C1C] rounded-xl px-3 py-2.5 text-sm outline-none focus:border-[#FF6B35]" />
        <div class="flex items-center gap-2 text-sm">
          <span>🕐</span><span class="font-black text-[#1C1C1C]">Thời gian giao: <span class="text-gray-500 font-medium">~20-25 phút</span></span>
        </div>
      </div>

    </div>

    {{-- RIGHT: Summary --}}
    <div class="mt-6 lg:mt-0 space-y-4 lg:sticky lg:top-24 lg:self-start">

      {{-- Voucher --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <button type="button" onclick="toggleVouchers()" class="w-full flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span>🏷️</span>
            <span class="font-black text-[#1C1C1C] text-sm" id="voucher-label">Chọn voucher</span>
          </div>
          <span class="text-gray-400">›</span>
        </button>
        <div id="voucher-list" class="mt-3 space-y-2 hidden">
          @forelse($vouchers ?? [] as $v)
          <div onclick="applyVoucher('{{ $v->code }}', {{ $v->value }}, '{{ $v->type }}', {{ $v->max_discount ?? 'null' }})"
            class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#FF6B35]/30 hover:border-[#FF6B35] hover:bg-orange-50 cursor-pointer transition-all">
            <div class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></div>
            <div class="flex-1">
              <div class="font-black text-[#1C1C1C] text-sm">{{ $v->code }}</div>
              <div class="text-xs text-gray-500">
                @if($v->type === 'flat') Giảm {{ number_format($v->value) }}đ
                @elseif($v->type === 'percent') Giảm {{ $v->value }}%{{ $v->max_discount ? ' tối đa '.number_format($v->max_discount).'đ' : '' }}
                @else Miễn phí vận chuyển
                @endif
                @if($v->min_order > 0) · Đơn từ {{ number_format($v->min_order) }}đ @endif
              </div>
            </div>
          </div>
          @empty
          <p class="text-xs text-gray-400 text-center py-2">Không có voucher khả dụng</p>
          @endforelse
        </div>
      </div>

      {{-- Payment --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <p class="font-black text-[#1C1C1C] text-sm mb-3 flex items-center gap-2">💳 Thanh toán</p>
        <div class="grid grid-cols-2 gap-2">
          @foreach([['id'=>'momo','label'=>'MoMo','emoji'=>'💜'],['id'=>'cod','label'=>'Tiền mặt','emoji'=>'💵'],['id'=>'zalopay','label'=>'ZaloPay','emoji'=>'🔵'],['id'=>'bank','label'=>'Chuyển khoản','emoji'=>'🏦']] as $pm)
          <button type="button" onclick="selectPayment('{{ $pm['id'] }}')" id="pm-{{ $pm['id'] }}"
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
            <span>Tạm tính</span>
            <span id="subtotal-display">{{ number_format($subtotal ?? 0) }}đ</span>
          </div>
          <div class="flex justify-between text-gray-600">
            <span>Phí vận chuyển</span>
            <span id="shipping-fee">15.000đ</span>
          </div>
          <div class="flex justify-between text-green-600 hidden" id="discount-row">
            <span>Voucher</span>
            <span id="discount-amount">-0đ</span>
          </div>
          <div class="border-t-2 border-[#1C1C1C] pt-2 flex justify-between font-black text-[#1C1C1C] text-base">
            <span>Tổng cộng</span>
            <span class="text-[#FF6B35]" id="total-display">{{ number_format(($subtotal ?? 0) + 15000) }}đ</span>
          </div>
        </div>
      </div>

      {{-- Checkout form --}}
      <form action="{{ route('client.checkout.post') }}" method="POST" id="checkout-form">
        @csrf
        <input type="hidden" name="payment_method" id="payment-input" value="momo" />
        <input type="hidden" name="delivery_mode" id="delivery-mode-input" value="delivery" />
        <input type="hidden" name="branch_id" value="1" />
        <input type="hidden" name="delivery_address" id="delivery-address-hidden" value="" />
        <button type="submit"
          class="w-full bg-[#FF6B35] text-white font-black py-4 rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all flex items-center justify-center gap-2 text-lg">
          ⚡ Đặt hàng ngay · <span id="btn-total">{{ number_format(($subtotal ?? 0) + 15000) }}đ</span>
        </button>
      </form>
      <p class="text-center text-xs text-gray-400">Bằng cách đặt hàng, bạn đồng ý với Điều khoản dịch vụ</p>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function addUpsell(id) {
  fetch('/cart/add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ product_id: id, quantity: 1 })
  }).then(() => { document.getElementById('upsell-widget')?.remove(); window.location.reload(); });
}


let deliveryMode = 'delivery', appliedDiscount = 0, isShippingVoucher = false;

function recalcTotal() {
  const ship = deliveryMode === 'delivery' ? 15000 : 0;
  const shipDiscount = isShippingVoucher ? Math.min(appliedDiscount, ship) : 0;
  const orderDiscount = isShippingVoucher ? 0 : appliedDiscount;
  const total = SUBTOTAL + ship - shipDiscount - orderDiscount;
  document.getElementById('shipping-fee').textContent = deliveryMode === 'delivery' ? '15.000đ' : 'Miễn phí';
  document.getElementById('total-display').textContent = total.toLocaleString('vi-VN') + 'đ';
  document.getElementById('btn-total').textContent = total.toLocaleString('vi-VN') + 'đ';
}

function setDelivery(mode) {
  deliveryMode = mode;
  document.getElementById('delivery-mode-input').value = mode;
  const d = document.getElementById('btn-delivery'), p = document.getElementById('btn-pickup');
  if (mode === 'delivery') {
    d.className = d.className.replace('bg-white text-[#1C1C1C]', 'bg-[#FF6B35] text-white');
    p.className = p.className.replace('bg-[#FF6B35] text-white', 'bg-white text-[#1C1C1C]');
    document.getElementById('delivery-info').classList.remove('hidden');
  } else {
    p.className = p.className.replace('bg-white text-[#1C1C1C]', 'bg-[#FF6B35] text-white');
    d.className = d.className.replace('bg-[#FF6B35] text-white', 'bg-white text-[#1C1C1C]');
    document.getElementById('delivery-info').classList.add('hidden');
  }
  recalcTotal();
}

function applyVoucher(code, value, type, maxDiscount) {
  const subtotal = SUBTOTAL;
  let discount = 0;
  if (type === 'flat') discount = value;
  else if (type === 'percent') discount = Math.min(subtotal * value / 100, maxDiscount || Infinity);
  else if (type === 'shipping') { discount = 15000; isShippingVoucher = true; }

  appliedDiscount = discount;
  document.getElementById('voucher-label').textContent = '✅ ' + code;
  document.getElementById('discount-row').classList.remove('hidden');
  document.getElementById('discount-amount').textContent = '-' + discount.toLocaleString('vi-VN') + 'đ';
  document.getElementById('voucher-list').classList.add('hidden');

  // Gọi API lưu vào session
  fetch('{{ route("client.cart.voucher") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ code })
  });

  recalcTotal();
}

function toggleVouchers() {
  document.getElementById('voucher-list').classList.toggle('hidden');
}

function selectPayment(id) {
  document.getElementById('payment-input').value = id;
  ['momo','cod','zalopay','bank'].forEach(p => {
    const el = document.getElementById('pm-' + p);
    if (p === id) { el.classList.add('border-[#FF6B35]','bg-orange-50','text-[#FF6B35]'); el.classList.remove('border-gray-200','text-gray-600'); }
    else { el.classList.remove('border-[#FF6B35]','bg-orange-50','text-[#FF6B35]'); el.classList.add('border-gray-200','text-gray-600'); }
  });
}

function updateQty(id, delta, price) {
  const el = document.getElementById('qty-' + id);
  let qty = Math.max(1, parseInt(el.textContent) + delta);
  el.textContent = qty;
  document.getElementById('item-price-' + id).textContent = (price * qty).toLocaleString('vi-VN') + 'đ';
  // Sync to server
  fetch(`/cart/${encodeURIComponent(id)}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ quantity: qty })
  });
  // Recalc subtotal
  let newSubtotal = 0;
  document.querySelectorAll('[id^="cart-item-"]').forEach(row => {
    const itemId = row.id.replace('cart-item-', '');
    const q = parseInt(document.getElementById('qty-' + itemId)?.textContent || 0);
    const p = parseInt(document.getElementById('item-price-' + itemId)?.dataset.price || 0);
    newSubtotal += q * p;
  });
}

// Sync delivery address trước khi submit
document.getElementById('checkout-form').addEventListener('submit', function() {
  document.getElementById('delivery-address-hidden').value =
    document.getElementById('delivery-address-input').value;
});
</script>
@endpush
