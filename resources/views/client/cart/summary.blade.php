@extends('layouts.client')
@section('title', 'Giỏ hàng')

@section('content')
    <div class="p-4 lg:p-8 max-w-7xl mx-auto">

        @if (session('success'))
            <div
                class="bg-green-50 border-2 border-green-400 rounded-xl px-4 py-3 mb-4 text-green-700 font-bold flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-red-50 border-2 border-red-400 rounded-xl px-4 py-3 mb-4 text-red-700 font-bold flex items-center gap-2">
                <span>⚠️</span> {{ session('error') }}
            </div>
        @endif

        <h1 class="font-black text-[#1C1C1C] text-2xl lg:text-3xl mb-6">Giỏ hàng của bạn 🛒</h1>

        {{-- Free shipping progress --}}
        @php
            $freeShipTarget = 100000;
            $remaining = max(0, $freeShipTarget - $subtotal);
            $pct = min(100, ($subtotal / $freeShipTarget) * 100);
        @endphp
        @if ($remaining > 0)
            <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🚵</span>
                    <p class="text-sm font-bold text-[#1C1C1C]">
                        Mua thêm <span class="text-[#FF6B35] font-black text-lg">{{ number_format($remaining) }}đ</span> để
                        được <span class="text-green-600 font-black text-lg">MIỄN PHÍ GIAO HÀNG!</span>
                    </p>
                </div>
                <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                    <div class="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full transition-all"
                        style="width: {{ $pct }}%"></div>
                    <span class="absolute text-lg -translate-x-1/2 -translate-y-1/2"
                        style="left: {{ $pct }}%; top: 50%;">🚵</span>
                </div>
            </div>
        @else
            <div class="bg-green-50 border-2 border-green-300 rounded-2xl p-4 mb-6 flex items-center gap-2">
                <span class="text-2xl">🎉</span>
                <p class="text-sm font-bold text-green-700">Bạn được <span class="font-black text-lg">MIỄN PHÍ GIAO
                        HÀNG</span> cho đơn này!</p>
            </div>
        @endif

        @if (!empty($cart))
            <div class="space-y-4 mb-8">
                @foreach ($cart as $item)
                    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden flex items-center p-4 lg:p-6"
                        id="cart-item-{{ $item['id'] }}">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                            class="w-20 lg:w-28 h-20 lg:h-28 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
                        <div class="flex-1 pl-4 space-y-2">
                            <div class="font-black text-[#1C1C1C] text-lg leading-tight">{{ $item['name'] }}</div>
                            @if (!empty($item['option_labels']))
                                <div class="text-sm text-gray-500">+ {{ implode(', ', $item['option_labels']) }}</div>
                            @endif
                            @if (!empty($item['note']))
                                <div class="text-sm text-orange-500 font-medium">Ghi chú: {{ $item['note'] }}</div>
                            @endif
                            <div class="flex items-center justify-between pt-2">
                                <span class="font-black text-[#FF6B35] text-xl" id="item-price-{{ $item['id'] }}"
                                    data-price="{{ $item['price'] }}">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
                                <div class="flex items-center gap-3">
                                    <button onclick="updateQty('{{ $item['id'] }}', -1, {{ $item['price'] }})"
                                        class="w-12 h-12 rounded-xl border-2 border-[#1C1C1C] bg-white flex items-center justify-center font-black text-xl shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none">−</button>
                                    <span class="font-black text-[#1C1C1C] text-2xl w-12 text-center min-w-[3rem]"
                                        id="qty-{{ $item['id'] }}">{{ $item['quantity'] }}</span>
                                    <button onclick="updateQty('{{ $item['id'] }}', 1, {{ $item['price'] }})"
                                        class="w-12 h-12 rounded-xl border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center font-black text-xl shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none">+</button>
                                    <form method="POST" action="{{ route('client.cart.remove', $item['id']) }}"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Xóa {{ $item['name'] }}?')"
                                            class="w-12 h-12 rounded-xl border-2 border-red-200 bg-red-50 text-red-500 flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C] hover:shadow-none transition-all">
                                            <span class="text-lg">🗑</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Upsell --}}
            @if ($upsell)
                <div
                    class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-6 flex items-center gap-4 mb-8">
                    <img src="{{ $upsell->image }}" alt="{{ $upsell->name }}"
                        class="w-20 h-20 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
                    <div class="flex-1">
                        <h3 class="font-black text-[#1C1C1C] text-lg mb-1">Thêm {{ $upsell->name }}?</h3>
                        <p class="text-[#1C1C1C]/70 text-sm mb-3">Chỉ {{ number_format($upsell->base_price) }}đ - Perfect
                            combo!</p>
                        <div class="flex gap-3">
                            <button onclick="addUpsell({{ $upsell->id }})"
                                class="bg-[#FF6B35] text-white font-black px-6 py-2.5 rounded-xl border border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">+
                                Thêm ngay</button>
                            <button onclick="this.parentElement.parentElement.parentElement.remove()"
                                class="text-[#1C1C1C] font-bold underline hover:no-underline">Bỏ qua</button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Summary & CTA --}}
            <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-6 lg:p-8">
                <h3 class="font-black text-[#1C1C1C] text-xl mb-4 flex items-center gap-2">Tóm tắt <span
                        class="text-2xl">({{ count($cart) }} món)</span></h3>
                <div class="space-y-3 mb-6 text-lg">
                    <div class="flex justify-between font-black">
                        <span>Tạm tính:</span>
                        <span class="text-[#FF6B35]">{{ number_format($subtotal) }}đ</span>
                    </div>
                    @if ($remaining > 0)
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Còn lại để freeship:</span>
                            <span class="font-black text-green-600">{{ number_format($remaining) }}đ</span>
                        </div>
                    @endif
                </div>
                <!-- Checkout moved to full form below -->
                <p class="text-center text-xs text-gray-400 mt-3">Bạn có thể chỉnh sửa giỏ hàng trước khi thanh toán</p>
            </div>
        @else
            <div class="text-center py-20 bg-white border-2 border-dashed border-gray-200 rounded-2xl">
                <div class="text-6xl mb-6">🛒</div>
                <h2 class="font-black text-2xl text-[#1C1C1C] mb-3">Giỏ hàng trống</h2>
                <p class="text-gray-500 text-lg mb-8">Chưa có món nào trong giỏ hàng của bạn</p>
                <a href="{{ route('client.menu') }}"
                    class="bg-[#FF6B35] text-white font-black px-8 py-4 rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] text-lg hover:shadow-none transition-all">🍜
                    Chọn món ngay</a>
            </div>
        @endif

    </div>

    @push('scripts')
        <script>
            function updateQty(id, delta, price) {
                const qtyEl = document.getElementById('qty-' + id);
                const priceEl = document.getElementById('item-price-' + id);
                let qty = Math.max(1, parseInt(qtyEl.textContent) + delta);
                qtyEl.textContent = qty;
                priceEl.textContent = (price * qty).toLocaleString('vi-VN') + 'đ';

                fetch(`/cart/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        quantity: qty
                    })
                });
            }

            function addUpsell(id) {
                fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: id,
                        quantity: 1
                    })
                }).then(r => r.json()).then(() => {
                    location.reload();
                });
            }
        </script>
    @endpush
@endsection
