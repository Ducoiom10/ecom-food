@extends('layouts.client')
@section('title', 'Thanh toán')

@push('styles')
    <style>
        @keyframes shimmer {
            0% {
                background-position: -800px 0
            }

            100% {
                background-position: 800px 0
            }
        }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 800px 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 12px;
        }

        /* Custom scrollbar for cart items */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #FF6B35 #f0f0f0;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #FF6B35;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #e55a2b;
        }
    </style>
@endpush

@section('content')
    <div id="main-cart" class="p-4 lg:p-8 max-w-7xl mx-auto">

        @if (session('success'))
            <div class="bg-green-50 border-2 border-green-400 rounded-xl px-4 py-3 mb-4 text-green-700 font-bold">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-2 border-red-400 rounded-xl px-4 py-3 mb-4 text-red-700 font-bold">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <h1 class="font-black text-[#1C1C1C] text-xl lg:text-2xl mb-6 lg:hidden">Thanh toán đơn hàng 💳</h1>

        {{-- Free shipping progress --}}
        @php
            $freeShipTarget = 100000;
            $remaining = max(0, $freeShipTarget - ($subtotal ?? 0));
            $pct = min(100, (($subtotal ?? 0) / $freeShipTarget) * 100);
        @endphp
        @if ($remaining > 0)
            <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-lg">🚵</span>
                    <p class="text-xs font-bold text-[#1C1C1C]">
                        Mua thêm <span class="text-[#FF6B35] font-black">{{ number_format($remaining) }}đ</span> để được
                        <span class="text-green-600 font-black">miễn phí vận chuyển!</span>
                    </p>
                </div>
                <div class="relative h-3 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                    <div class="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full transition-all"
                        style="width: {{ $pct }}%"></div>
                    <span class="absolute text-base"
                        style="left: {{ $pct }}%; top: 50%; transform: translate(-50%,-50%)">🚵</span>
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

{{-- Cart items - New Beautiful Design with scrollable container --}}
                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-1 custom-scrollbar" id="cart-items">
                    @forelse($cart ?? [] as $item)
                        <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden hover:shadow-[2px_2px_0px_#1C1C1C] transition-all"
                            id="cart-item-{{ $item['id'] }}" data-options="{{ json_encode($item['options'] ?? []) }}">
                            <div class="flex p-3 gap-3">
                                {{-- Product Image --}}
                                <div class="relative w-20 h-20 lg:w-24 lg:h-24 flex-shrink-0 rounded-xl overflow-hidden border border-gray-100">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                        class="w-full h-full object-cover" />
                                    <div class="absolute -top-1 -left-1 bg-[#FF6B35] text-white text-[10px] font-black px-1.5 py-0.5 rounded-lg">
                                        x{{ $item['quantity'] }}
                                    </div>
                                </div>

                                {{-- Product Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <h3 class="font-black text-[#1C1C1C] text-sm lg:text-base truncate">{{ $item['name'] }}</h3>
                                            @if (!empty($item['option_labels']))
                                                <div class="text-xs text-[#FF6B35] font-medium mt-0.5 flex flex-wrap gap-1">
                                                    @foreach($item['option_labels'] as $label)
                                                        <span class="bg-[#FFF5F0] px-1.5 py-0.5 rounded-md">{{ $label }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if (!empty($item['note']))
                                                <div class="text-xs text-orange-500 mt-1 italic">📝 {{ $item['note'] }}</div>
                                            @endif
                                        </div>
                                        <span class="font-black text-[#FF6B35] text-base whitespace-nowrap" id="item-price-{{ $item['id'] }}"
                                            data-price="{{ $item['price'] }}">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex items-center justify-between mt-3">
                                        <div class="flex items-center gap-1">
                                            <button type="button"
                                                onclick="updateQty('{{ $item['id'] }}', -1, {{ $item['price'] }})"
                                                class="w-9 h-9 rounded-xl border-2 border-[#1C1C1C] bg-white flex items-center justify-center font-black text-lg shadow-[2px_2px_0px_#1C1C1C] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[1px_1px_0px_#1C1C1C] transition-all">−</button>
                                            <span class="font-black text-[#1C1C1C] text-base w-10 text-center bg-gray-50 py-1 rounded-lg"
                                                id="qty-{{ $item['id'] }}">{{ $item['quantity'] }}</span>
                                            <button type="button"
                                                onclick="updateQty('{{ $item['id'] }}', 1, {{ $item['price'] }})"
                                                class="w-9 h-9 rounded-xl border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center font-black text-lg shadow-[2px_2px_0px_#1C1C1C] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[1px_1px_0px_#1C1C1C] transition-all">+</button>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <button type="button" onclick="openEditModal('{{ $item['id'] }}')"
                                                class="h-9 px-3 rounded-xl border-2 border-[#FFD23F] bg-[#FFD23F] text-[#1C1C1C] flex items-center justify-center font-bold text-sm shadow-[2px_2px_0px_#1C1C1C] hover:bg-[#FFE566] transition-all"
                                                title="Sửa/Đổi món">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2l7.293-7.293z" />
                                                </svg>
                                                Sửa
                                            </button>
                                            <form method="POST" action="{{ route('client.cart.remove', $item['id']) }}"
                                                class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="h-9 w-9 rounded-xl border-2 border-red-200 bg-red-50 text-red-500 flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:bg-red-100 transition-all"
                                                    title="Xóa">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1H6a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center">
                            <div class="text-6xl mb-4">🥣</div>
                            <h3 class="font-black text-[#1C1C1C] text-lg mb-2">Giỏ hàng trống</h3>
                            <p class="text-gray-500 text-sm mb-4">Hãy thêm món ngon nào đó!</p>
                            <a href="{{ route('client.menu') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-[#FF6B35] text-white font-black rounded-xl shadow-[3px_3px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all">
                                <span>🍜</span> Xem thực đơn
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Upsell widget (removed - require variants) --}}
                {{-- @if ($upsell)
                    <div id="upsell-widget"
                        class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 flex items-center gap-3">
                        <img src="{{ $upsell->image }}" alt="{{ $upsell->name }}"
                            class="w-12 h-12 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
                        <div class="flex-1">
                            <p class="font-black text-[#1C1C1C] text-xs">Thêm {{ $upsell->name }} chỉ
                                {{ number_format($upsell->base_price) }}đ?</p>
                            <p class="text-[10px] text-[#1C1C1C]/60">Perfect combo với đơn của bạn 😋</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <button onclick="addUpsell({{ $upsell->id }})"
                                class="bg-[#FF6B35] text-white text-[10px] font-black px-2 py-1 rounded-lg border border-[#1C1C1C]">+
                                Thêm</button>
                            <button onclick="document.getElementById('upsell-widget').remove()"
                                class="text-[10px] text-gray-500">Bỏ qua</button>
                        </div>
                    </div>
                @endif --}}

                {{-- Delivery address --}}
                <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 space-y-3"
                    id="delivery-info">
                    <div class="flex items-center gap-2">
                        <span>📍</span><span class="font-black text-[#1C1C1C] text-sm">Địa chỉ giao hàng</span>
                    </div>
                    <input type="text" id="delivery-address-input" placeholder="Nhập địa chỉ giao hàng..."
                        class="w-full border-2 border-[#1C1C1C] rounded-xl px-3 py-2.5 text-sm outline-none focus:border-[#FF6B35]" />
                    <div class="flex items-center gap-2 text-sm">
                        <span>🕐</span><span class="font-black text-[#1C1C1C]">Thời gian giao: <span
                                class="text-gray-500 font-medium">~20-25 phút</span></span>
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
                                        @if ($v->type === 'flat')
                                            Giảm {{ number_format($v->value) }}đ
                                        @elseif($v->type === 'percent')
                                            Giảm
                                            {{ $v->value }}%{{ $v->max_discount ? ' tối đa ' . number_format($v->max_discount) . 'đ' : '' }}
                                        @else
                                            Miễn phí vận chuyển
                                        @endif
                                        @if ($v->min_order > 0)
                                            · Đơn từ {{ number_format($v->min_order) }}đ
                                        @endif
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
                        @foreach ([['id' => 'momo', 'label' => 'MoMo', 'emoji' => '💜'], ['id' => 'cod', 'label' => 'Tiền mặt', 'emoji' => '💵'], ['id' => 'zalopay', 'label' => 'ZaloPay', 'emoji' => '🔵'], ['id' => 'bank', 'label' => 'Chuyển khoản', 'emoji' => '🏦']] as $pm)
                            <button type="button" onclick="selectPayment('{{ $pm['id'] }}')"
                                id="pm-{{ $pm['id'] }}"
                                class="flex items-center gap-2 py-2.5 px-3 rounded-xl border-2 text-sm font-bold transition-all {{ $pm['id'] === 'momo' ? 'border-[#FF6B35] bg-orange-50 text-[#FF6B35]' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
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
                        <div
                            class="border-t-2 border-[#1C1C1C] pt-2 flex justify-between font-black text-[#1C1C1C] text-base">
                            <span>Tổng cộng</span>
                            <span class="text-[#FF6B35]"
                                id="total-display">{{ number_format(($subtotal ?? 0) + 15000) }}đ</span>
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
        const SUBTOTAL = {{ $subtotal ?? 0 }};

        // addUpsell removed - require variants


        let deliveryMode = 'delivery',
            appliedDiscount = 0,
            isShippingVoucher = false,
            editingItem = null,
            editingSize = 'M',
            editingOptions = [],
            basePrice = 0,
            editingQty = 1;

        // Size prices
        const SIZE_PRICES = {
            'S': 0,
            'M': 5000,
            'L': 10000
        };

// Available toppings for edit modal - numeric IDs match database
        const AVAILABLE_TOPPINGS = [{
                id: 4,
                name: 'Trứng lòng đào',
                price: 5000
            },
            {
                id: 5,
                name: 'Trứng luộc',
                price: 3000
            },
            {
                id: 6,
                name: 'Thịt bò',
                price: 15000
            },
            {
                id: 7,
                name: 'Rau muống',
                price: 5000
            },
            {
                id: 8,
                name: 'Nấm',
                price: 5000
            },
            {
                id: 9,
                name: 'Tàu hủ',
                price: 3000
            },
            {
                id: 10,
                name: 'Bột chiên',
                price: 8000
            },
            {
                id: 11,
                name: 'Kim chi',
                price: 5000
            }
        ];

        // Helper to convert numeric topping IDs to selected array for rendering
        function getToppingIdsFromNumeric(numericOptions) {
            return numericOptions.slice(1).map(String); // Skip first (size), convert rest to strings
        }

        function openEditModal(itemId) {
            editingItem = itemId;
            // Get base price & qty from the item row
            const row = document.getElementById('cart-item-' + itemId);
            basePrice = parseInt(row.querySelector('[id^="item-price-"]').dataset.price);
            // Get current quantity
            const qtySpan = document.getElementById('qty-' + itemId);
            editingQty = parseInt(qtySpan?.textContent || 1);
            // Get current options from data attribute
            let currentOptions = [];
            try {
                const optionsData = row.dataset.options;
                if (optionsData && optionsData !== 'null' && optionsData !== '[]') {
                    currentOptions = JSON.parse(optionsData);
                }
            } catch (e) {
                currentOptions = [];
            }
            // Determine size from options (first 3 IDs typically for sizes: 1=S, 2=M, 3=L)
            const sizeOptionIds = {
                1: 'S',
                2: 'M',
                3: 'L'
            };
            if (currentOptions.length > 0) {
                const firstOpt = currentOptions[0];
                editingSize = sizeOptionIds[firstOpt] || 'M';
                // Rest are toppings (skip first which is size)
                editingOptions = currentOptions.slice(1).map(String);
            } else {
                // Fallback: detect from text labels
                const optionLabelText = row.querySelector('.text-gray-500')?.textContent || '';
                if (optionLabelText.includes('S') || optionLabelText.includes('Size S')) {
                    editingSize = 'S';
                } else if (optionLabelText.includes('L')) {
                    editingSize = 'L';
                } else {
                    editingSize = 'M';
                }
                editingOptions = [];
            }
            document.getElementById('edit-modal').classList.remove('hidden');
            updateSizeButtons();
            renderToppings();
            updateEditTotal();
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
            editingItem = null;
        }

        function selectSize(size) {
            editingSize = size;
            updateSizeButtons();
            updateEditTotal();
        }

        function updateSizeButtons() {
            ['S', 'M', 'L'].forEach(s => {
                const btn = document.getElementById('size-' + s);
                if (s === editingSize) {
                    btn.className =
                        'flex-1 py-2 rounded-xl border-2 border-[#FF6B35] bg-[#FF6B35] text-white font-black text-sm';
                } else {
                    btn.className = 'flex-1 py-2 rounded-xl border-2 border-gray-200 font-black text-sm';
                }
            });
        }

function updateEditTotal() {
            const sizePrice = SIZE_PRICES[editingSize] || 0;
            const toppingPrice = editingOptions.reduce((sum, id) => {
                // Convert to number for matching since editingOptions stores numeric IDs as strings
                const numId = parseInt(id);
                const t = AVAILABLE_TOPPINGS.find(x => x.id === numId);
                return sum + (t ? t.price : 0);
            }, 0);
            const total = basePrice + sizePrice + toppingPrice;
            document.getElementById('edit-total-price').textContent = total.toLocaleString('vi-VN') + 'đ';
        }

        function toggleTopping(toppingId) {
            // Convert to string for consistent comparison
            const strId = String(toppingId);
            if (editingOptions.includes(strId)) {
                editingOptions = editingOptions.filter(id => id !== strId);
            } else {
                editingOptions.push(strId);
            }
            renderToppings();
        }

        function renderToppings() {
            const list = document.getElementById('toppings-list');
            list.innerHTML = AVAILABLE_TOPPINGS.map(t => {
                const isSelected = editingOptions.includes(String(t.id));
                return `
                <div onclick="toggleTopping(${t.id})"
                    class="flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition-all ${isSelected ? 'border-[#FF6B35] bg-orange-50' : 'border-gray-200 hover:border-gray-300'}">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center ${isSelected ? 'bg-[#FF6B35] border-[#FF6B35]' : 'border-gray-300'}">
                            ${isSelected ? '✓' : ''}
                        </div>
                        <span class="font-bold text-sm text-[#1C1C1C]">${t.name}</span>
                    </div>
                    <span class="font-black text-[#FF6B35] text-sm">+${t.price.toLocaleString('vi-VN')}đ</span>
                </div>
            `}).join('');
        }

function saveToppings() {
            if (!editingItem) return;
            // Map size to option value IDs (this should come from backend in real app)
            const sizeOptionIds = {
                'S': 1,
                'M': 2,
                'L': 3
            };
            // Convert editingOptions from strings to numbers for the API
            const toppingIds = editingOptions.map(id => parseInt(id));
            const selectedOptions = [sizeOptionIds[editingSize], ...toppingIds];
            console.log('Saving options:', selectedOptions, 'qty:', editingQty);
            // Update via API - INCLUDE current quantity!
            fetch(`/cart/${encodeURIComponent(editingItem)}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    options: selectedOptions,
                    quantity: editingQty
                })
            }).then(res => {
                console.log('Response:', res.ok, res.status);
                if (res.ok) window.location.reload();
            }).catch(err => console.error('Error:', err));
            closeEditModal();
        }

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
            const d = document.getElementById('btn-delivery'),
                p = document.getElementById('btn-pickup');
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
            else if (type === 'shipping') {
                discount = 15000;
                isShippingVoucher = true;
            }

            appliedDiscount = discount;
            document.getElementById('voucher-label').textContent = '✅ ' + code;
            document.getElementById('discount-row').classList.remove('hidden');
            document.getElementById('discount-amount').textContent = '-' + discount.toLocaleString('vi-VN') + 'đ';
            document.getElementById('voucher-list').classList.add('hidden');

            // Gọi API lưu vào session
            fetch('{{ route('client.cart.voucher') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    code
                })
            });

            recalcTotal();
        }

        function toggleVouchers() {
            document.getElementById('voucher-list').classList.toggle('hidden');
        }

        function selectPayment(id) {
            document.getElementById('payment-input').value = id;
            ['momo', 'cod', 'zalopay', 'bank'].forEach(p => {
                const el = document.getElementById('pm-' + p);
                if (p === id) {
                    el.classList.add('border-[#FF6B35]', 'bg-orange-50', 'text-[#FF6B35]');
                    el.classList.remove('border-gray-200', 'text-gray-600');
                } else {
                    el.classList.remove('border-[#FF6B35]', 'bg-orange-50', 'text-[#FF6B35]');
                    el.classList.add('border-gray-200', 'text-gray-600');
                }
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
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    quantity: qty
                })
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

{{-- Edit Modal: Size + Toppings --}}
<div id="edit-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 w-full max-w-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-black text-[#1C1C1C] text-lg">Sửa/Đổi món</h3>
            <button type="button" onclick="closeEditModal()"
                class="w-8 h-8 rounded-full border-2 border-gray-200 flex items-center justify-center">✕</button>
        </div>

        {{-- Size selection --}}
        <p class="text-xs text-gray-500 mb-2">Chọn size:</p>
        <div class="flex gap-2 mb-3" id="size-list">
            <button type="button" onclick="selectSize('S')" id="size-S"
                class="flex-1 py-2 rounded-xl border-2 border-gray-200 font-black text-sm">S</button>
            <button type="button" onclick="selectSize('M')" id="size-M"
                class="flex-1 py-2 rounded-xl border-2 border-[#FF6B35] bg-[#FF6B35] text-white font-black text-sm">M</button>
            <button type="button" onclick="selectSize('L')" id="size-L"
                class="flex-1 py-2 rounded-xl border-2 border-gray-200 font-black text-sm">L</button>
        </div>

        <p class="text-xs text-gray-500 mb-2">Thêm topping:</p>
        <div id="toppings-list" class="space-y-2 max-h-40 overflow-y-auto"></div>

        <div class="border-t-2 border-gray-100 mt-3 pt-2 flex justify-between">
            <span class="text-sm text-gray-500">Tổng cộng:</span>
            <span class="font-black text-[#FF6B35]" id="edit-total-price">0đ</span>
        </div>

        <div class="flex gap-2 mt-3">
            <button type="button" onclick="closeEditModal()"
                class="flex-1 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-bold text-sm">Hủy</button>
            <button type="button" onclick="saveToppings()"
                class="flex-1 py-3 rounded-xl bg-[#FF6B35] text-white font-black text-sm border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">Lưu
                ✓</button>
        </div>
    </div>
</div>
