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
    <div class="hidden lg:block space-y-4 mt-0">
      <div class="skeleton h-32 w-full"></div>
      <div class="skeleton h-40 w-full"></div>
      <div class="skeleton h-28 w-full"></div>
      <div class="skeleton h-14 w-full"></div>
    </div>
  </div>
</div>

<div id="main-cart" class="hidden p-4 lg:p-8 max-w-7xl mx-auto">
  <h1 class="font-black text-[#1C1C1C] text-xl lg:text-2xl mb-6 lg:hidden">Giỏ hàng của bạn 🛒</h1>

  <div class="lg:grid lg:grid-cols-3 lg:gap-8">

    {{-- LEFT: Cart items --}}
    <div class="lg:col-span-2 space-y-4">

      {{-- Free shipping progress --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xl">🛵</span>
          <p class="text-sm font-bold text-[#1C1C1C]">
            Mua thêm <span class="text-[#FF6B35] font-black">35.000đ</span> để được <span class="text-green-600 font-black">miễn phí vận chuyển!</span>
          </p>
        </div>
        <div class="h-3 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
          <div class="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full transition-all" style="width: 65%"></div>
        </div>
      </div>

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
      <div class="space-y-3" id="cart-items">
        @foreach($cartItems ?? [] as $item)
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
              <div class="text-xs text-gray-400">Size: {{ $item['size'] }}</div>
            </div>
            <div class="flex items-center justify-between mt-2">
              <span class="font-black text-[#FF6B35] text-base" id="item-price-{{ $item['id'] }}">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
              <div class="flex items-center gap-2">
                <button onclick="updateQty('{{ $item['id'] }}', -1, {{ $item['price'] }})" class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-white flex items-center justify-center font-black shadow-[1px_1px_0px_#1C1C1C]">−</button>
                <span class="font-black text-[#1C1C1C] text-sm w-6 text-center" id="qty-{{ $item['id'] }}">{{ $item['quantity'] }}</span>
                <button onclick="updateQty('{{ $item['id'] }}', 1, {{ $item['price'] }})" class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C]">+</button>
                <button onclick="removeItem('{{ $item['id'] }}')" class="w-8 h-8 rounded-lg border-2 border-red-200 bg-red-50 text-red-500 flex items-center justify-center">🗑</button>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      {{-- Upsell --}}
      <div class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 flex items-center gap-3" id="upsell-box">
        <img src="https://images.unsplash.com/photo-1776178393305-be4c1097fae5?w=100&q=80" alt="" class="w-14 h-14 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
        <div class="flex-1">
          <p class="font-black text-[#1C1C1C] text-sm">Thêm Chả giò giòn chỉ 25.000đ?</p>
          <p class="text-xs text-[#1C1C1C]/60">Perfect combo với mì trộn 😋</p>
        </div>
        <div class="flex flex-col gap-1">
          <button class="bg-[#FF6B35] text-white text-xs font-black px-3 py-1.5 rounded-lg border border-[#1C1C1C]">+ Thêm</button>
          <button onclick="document.getElementById('upsell-box').remove()" class="text-xs text-gray-600">Bỏ qua</button>
        </div>
      </div>

      {{-- Delivery info --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 space-y-3" id="delivery-info">
        <div class="flex items-center gap-2">
          <span>📍</span><span class="font-black text-[#1C1C1C] text-sm">Địa chỉ giao hàng</span>
        </div>
        <input type="text" value="123 Nguyễn Huệ, Phường Bến Nghé, Q.1, TP.HCM"
          class="w-full border-2 border-[#1C1C1C] rounded-xl px-3 py-2.5 text-sm outline-none focus:border-[#FF6B35]" />
        <div class="flex items-center gap-2 text-sm">
          <span>🕐</span><span class="font-black text-[#1C1C1C]">Thời gian giao: <span class="text-gray-500 font-medium">~20-25 phút</span></span>
        </div>
      </div>

    </div>

    {{-- RIGHT: Summary (sticky desktop) --}}
    <div class="mt-6 lg:mt-0 space-y-4 lg:sticky lg:top-24 lg:self-start">

      {{-- Voucher --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <button onclick="toggleVouchers()" class="w-full flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span>🏷️</span>
            <span class="font-black text-[#1C1C1C] text-sm" id="voucher-label">Chọn voucher</span>
          </div>
          <span class="text-gray-400">›</span>
        </button>
        <div id="voucher-list" class="mt-3 space-y-2 hidden">
          @foreach([['code'=>'LUNCH15K','label'=>'Giảm 15.000đ cho đơn từ 80k','discount'=>15000],['code'=>'BANANH20','label'=>'Giảm 20% tối đa 30k','discount'=>30000],['code'=>'FREESHIP','label'=>'Miễn phí vận chuyển','discount'=>15000]] as $v)
          <div onclick="applyVoucher('{{ $v['code'] }}', {{ $v['discount'] }})"
            class="flex items-center gap-3 p-3 rounded-xl border-2 border-[#FF6B35]/30 hover:border-[#FF6B35] hover:bg-orange-50 cursor-pointer transition-all">
            <div class="w-2 h-2 rounded-full bg-green-500"></div>
            <div class="flex-1">
              <div class="font-black text-[#1C1C1C] text-sm">{{ $v['code'] }}</div>
              <div class="text-xs text-gray-500">{{ $v['label'] }}</div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Payment --}}
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <p class="font-black text-[#1C1C1C] text-sm mb-3 flex items-center gap-2">💳 Thanh toán</p>
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
            <span>Tạm tính</span>
            <span id="subtotal">{{ number_format($subtotal ?? 0) }}đ</span>
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
            <span class="text-[#FF6B35]" id="total">{{ number_format(($subtotal ?? 0) + 15000) }}đ</span>
          </div>
        </div>
      </div>

      {{-- Checkout --}}
      <form action="{{ route('client.checkout') }}" method="POST">
        @csrf
        <input type="hidden" name="payment_method" id="payment-input" value="momo" />
        <button type="submit"
          class="w-full bg-[#FF6B35] text-white font-black py-4 rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all flex items-center justify-center gap-2 text-lg">
          ⚡ Đặt hàng ngay · <span id="btn-total">{{ number_format(($subtotal ?? 0) + 15000) }}đ</span>
        </button>
      </form>
      <p class="text-center text-xs text-gray-400 mt-2">Bằng cách đặt hàng, bạn đồng ý với Điều khoản dịch vụ</p>
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

let deliveryMode = 'delivery', appliedDiscount = 0;

function setDelivery(mode) {
  deliveryMode = mode;
  const d = document.getElementById('btn-delivery'), p = document.getElementById('btn-pickup');
  const info = document.getElementById('delivery-info');
  if (mode === 'delivery') {
    d.className = d.className.replace('bg-white text-[#1C1C1C]','bg-[#FF6B35] text-white');
    p.className = p.className.replace('bg-[#1C1C1C] text-white','bg-white text-[#1C1C1C]');
    document.getElementById('shipping-fee').textContent = '15.000đ';
    info.classList.remove('hidden');
  } else {
    p.className = p.className.replace('bg-white text-[#1C1C1C]','bg-[#1C1C1C] text-white');
    d.className = d.className.replace('bg-[#FF6B35] text-white','bg-white text-[#1C1C1C]');
    document.getElementById('shipping-fee').textContent = 'Miễn phí';
    info.classList.add('hidden');
  }
  recalcTotal();
}
function applyVoucher(code, discount) {
  appliedDiscount = discount;
  document.getElementById('voucher-label').textContent = 'Đã dùng: ' + code;
  document.getElementById('discount-row').classList.remove('hidden');
  document.getElementById('discount-amount').textContent = '-' + discount.toLocaleString('vi-VN') + 'đ';
  document.getElementById('voucher-list').classList.add('hidden');
  recalcTotal();
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
function recalcTotal() {
  const sub = parseInt(document.getElementById('subtotal').textContent.replace(/\D/g,'')) || 0;
  const ship = deliveryMode === 'delivery' ? 15000 : 0;
  const total = sub + ship - appliedDiscount;
  document.getElementById('total').textContent = total.toLocaleString('vi-VN') + 'đ';
  document.getElementById('btn-total').textContent = total.toLocaleString('vi-VN') + 'đ';
}
function updateQty(id, delta, price) {
  const el = document.getElementById('qty-' + id);
  let qty = Math.max(1, parseInt(el.textContent) + delta);
  el.textContent = qty;
  document.getElementById('item-price-' + id).textContent = (price * qty).toLocaleString('vi-VN') + 'đ';
}
function removeItem(id) { document.getElementById('cart-item-' + id).remove(); }
</script>
@endpush
