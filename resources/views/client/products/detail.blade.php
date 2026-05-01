@extends('layouts.client')
@section('title', $product->name)

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
            border-radius: 8px;
        }
    </style>
@endpush

@section('content')

    {{-- Skeleton Screen --}}
    <div id="skeleton-screen" class="p-4 lg:p-8 max-w-7xl mx-auto">
        <div class="lg:grid lg:grid-cols-2 lg:gap-10">
            <div class="skeleton h-72 lg:h-[480px] w-full mb-4 lg:mb-0"></div>
            <div class="space-y-4">
                <div class="skeleton h-8 w-3/4"></div>
                <div class="skeleton h-4 w-1/2"></div>
                <div class="skeleton h-16 w-full"></div>
                <div class="skeleton h-12 w-full"></div>
                <div class="skeleton h-12 w-full"></div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div id="main-content" class="hidden p-4 lg:p-8 max-w-7xl mx-auto pb-32 lg:pb-8">

        <a href="{{ route('client.menu') ?? '#' }}"
            class="inline-flex items-center gap-2 text-sm font-bold text-[#1C1C1C] mb-4 hover:text-[#FF6B35] transition-colors">
            ← Quay lại thực đơn
        </a>

        <div class="lg:grid lg:grid-cols-2 lg:gap-10 lg:items-start">

            {{-- Cột trái: Hình ảnh & Icon --}}
            <div class="lg:sticky lg:top-24">
                <div
                    class="relative overflow-hidden rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] aspect-[4/3]">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" />
                    @if ($product->is_new)
                        <div
                            class="absolute top-3 left-3 bg-[#FFD23F] border border-[#1C1C1C] text-[#1C1C1C] text-xs font-black px-2 py-1 rounded-full shadow-[1px_1px_0px_#1C1C1C]">
                            ✨ MỚI</div>
                    @endif
                    @if ($product->is_best_seller)
                        <div
                            class="absolute top-3 right-3 bg-[#FF6B35] text-white text-xs font-black px-2 py-1 rounded-full shadow-[1px_1px_0px_#1C1C1C]">
                            🔥 BÁN CHẠY</div>
                    @endif
                </div>

                {{-- Stat cards --}}
                <div class="hidden lg:grid grid-cols-3 gap-3 mt-4">
                    <div class="bg-white border-2 border-[#1C1C1C] rounded-xl p-3 text-center shadow-[2px_2px_0px_#1C1C1C]">
                        <div class="text-xl mb-1">🍽️</div>
                        <div class="font-black text-[#1C1C1C] text-sm">{{ $product->category?->name }}</div>
                        <div class="text-xs text-gray-400">Danh mục</div>
                    </div>
                    <div class="bg-white border-2 border-[#1C1C1C] rounded-xl p-3 text-center shadow-[2px_2px_0px_#1C1C1C]">
                        <div class="text-xl mb-1">🔥</div>
                        <div class="font-black text-[#1C1C1C] text-sm">{{ $product->calories ?? '—' }} kcal</div>
                        <div class="text-xs text-gray-400">Calories</div>
                    </div>
                    <div class="bg-white border-2 border-[#1C1C1C] rounded-xl p-3 text-center shadow-[2px_2px_0px_#1C1C1C]">
                        <div class="text-xl mb-1">⏱️</div>
                        <div class="font-black text-[#1C1C1C] text-sm">~15 phút</div>
                        <div class="text-xs text-gray-400">Giao hàng</div>
                    </div>
                </div>
            </div> {{-- Kết thúc cột trái --}}

            {{-- Cột phải: Thông tin, Giá, Options, CTA --}}
            <div class="mt-4 lg:mt-0 space-y-5">

                <div>
                    <h1 class="font-black text-[#1C1C1C] text-2xl lg:text-3xl leading-tight">{{ $product->name }}</h1>
                    <p class="text-gray-600 text-sm lg:text-base mt-3 leading-relaxed">{{ $product->description }}</p>
                </div>

                <div
                    class="flex items-center justify-between bg-[#FAFAF8] border-2 border-[#1C1C1C] rounded-xl px-4 py-3 shadow-[2px_2px_0px_#1C1C1C]">
                    <div>
                        <div class="text-xs text-gray-400 font-medium">Giá từ</div>
                        <div class="font-black text-[#FF6B35] text-2xl lg:text-3xl">
                            {{ number_format($product->base_price) }}đ</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400 font-medium">Dự kiến giao</div>
                        <div class="font-black text-[#1C1C1C] text-base flex items-center gap-1 justify-end">
                            <span class="text-blue-500">🕐</span> ~15 phút
                        </div>
                    </div>
                </div>

                @if ($product->calories)
                    <details class="bg-green-50 border-2 border-green-200 rounded-xl overflow-hidden group">
                        <summary
                            class="px-4 py-3 font-bold text-sm text-green-800 cursor-pointer flex items-center justify-between list-none">
                            <span>🥗 Thông tin dinh dưỡng</span>
                            <span class="text-green-500 group-open:rotate-180 transition-transform">▾</span>
                        </summary>
                        <div class="px-4 pb-4 space-y-2">
                            <div class="flex justify-between text-sm py-1.5 border-b border-green-100">
                                <span class="text-gray-600">Calories</span>
                                <span class="font-bold text-[#1C1C1C]">{{ $product->calories }} kcal</span>
                            </div>
                        </div>
                    </details>
                @endif

                {{-- Options (Size, Topping) --}}
                @if ($product->options)
                    @foreach ($product->options as $option)
                        @php
                            $isRequired = $option->type === 'required';
                        @endphp
                        <div>
                            <h3 class="font-black text-[#1C1C1C] text-base mb-3">
                                {{ $option->name }}
                                @if (!$isRequired)
                                    <span class="text-gray-400 font-medium text-sm">(tuỳ chọn)</span>
                                @endif
                            </h3>

                            @if ($isRequired)
                                <div class="flex gap-2 flex-wrap">
                                    @foreach ($option->values as $val)
                                        <label class="flex-1 cursor-pointer min-w-[80px]">
                                            <input type="radio" name="option_{{ $option->id }}"
                                                value="{{ $val->id }}" class="hidden peer"
                                                {{ $loop->first ? 'checked' : '' }} onchange="updateOptionPrice()" />
                                            <div
                                                class="peer-checked:bg-[#FF6B35] peer-checked:text-white peer-checked:border-[#FF6B35] peer-checked:shadow-none border-2 border-[#1C1C1C] rounded-xl py-2.5 text-center text-sm font-bold cursor-pointer transition-all shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none">
                                                <div>{{ $val->label }}</div>
                                                @if ($val->extra_price > 0)
                                                    <div class="text-xs opacity-80">
                                                        +{{ number_format($val->extra_price) }}đ</div>
                                                @else
                                                    <div class="text-xs opacity-60">Mặc định</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach ($option->values as $val)
                                        <label
                                            class="flex items-center justify-between bg-white border-2 border-[#1C1C1C] rounded-xl px-4 py-3 cursor-pointer hover:bg-orange-50 hover:border-[#FF6B35] transition-all shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none group">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-5 h-5 border-2 border-[#1C1C1C] rounded-md flex items-center justify-center group-has-[:checked]:bg-[#FF6B35] group-has-[:checked]:border-[#FF6B35] transition-all flex-shrink-0">
                                                    <input type="checkbox" name="toppings[]" value="{{ $val->id }}"
                                                        class="opacity-0 absolute" onchange="updateOptionPrice()" />
                                                    <svg class="w-3 h-3 text-white hidden group-has-[:checked]:block"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                                <span class="font-bold text-sm text-[#1C1C1C]">{{ $val->label }}</span>
                                            </div>
                                            <span
                                                class="text-[#FF6B35] font-black text-sm">+{{ number_format($val->extra_price) }}đ</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                <div>
                    <h3 class="font-black text-[#1C1C1C] text-base mb-2">Ghi chú cho bếp</h3>
                    <textarea id="pdp-note" placeholder="Ví dụ: ít hành, không cay, thêm sốt..." rows="2"
                        class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] resize-none transition-all"></textarea>
                </div>

                {{-- CTA Desktop --}}
                <div
                    class="hidden lg:block bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="text-xs text-gray-400">Tổng cộng</div>
                            <div class="font-black text-[#FF6B35] text-2xl" id="pdp-total-desk">
                                {{ number_format($product->base_price) }}đ</div>
                        </div>
                        <div
                            class="flex items-center gap-2 border-2 border-[#1C1C1C] rounded-xl overflow-hidden shadow-[2px_2px_0px_#1C1C1C]">
                            <button onclick="changeQty(-1)"
                                class="w-10 h-10 flex items-center justify-center font-black text-xl hover:bg-gray-100 transition-colors">−</button>
                            <span class="font-black text-[#1C1C1C] text-lg min-w-[32px] text-center"
                                id="pdp-qty-desk">1</span>
                            <button onclick="changeQty(1)"
                                class="w-10 h-10 flex items-center justify-center font-black text-xl bg-[#FF6B35] text-white hover:bg-[#e55a25] transition-colors">+</button>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <button onclick="addToCartPDP(false)"
                            class="w-full bg-[#FF6B35] text-white font-black py-4 rounded-xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all text-base flex items-center justify-center gap-2">
                            🛒 Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>

                {{-- Sản phẩm liên quan --}}
                @if ($related && $related->count() > 0)
                    <div class="hidden lg:block mt-6">
                        <h3 class="font-black text-[#1C1C1C] text-base mb-3">Có thể bạn thích</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($related as $rel)
                                <a href="#"
                                    class="flex gap-2 bg-white border-2 border-[#1C1C1C] rounded-xl p-2 hover:bg-orange-50 hover:border-[#FF6B35] transition-all shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none">
                                    <img src="{{ $rel->image }}" alt="{{ $rel->name }}"
                                        class="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" />
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-xs text-[#1C1C1C] line-clamp-2 leading-tight">
                                            {{ $rel->name }}</div>
                                        <div class="font-black text-[#FF6B35] text-sm mt-1">
                                            {{ number_format($rel->base_price) }}đ</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div> {{-- Kết thúc cột phải --}}

        </div> {{-- Kết thúc Grid 2 cột chính --}}

        {{-- Mobile sticky CTA --}}
        <div
            class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t-2 border-[#1C1C1C] px-4 py-3 z-40 shadow-[0_-4px_0px_#1C1C1C]">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <div class="text-xs text-gray-400">Tổng cộng</div>
                    <div class="font-black text-[#FF6B35] text-xl" id="pdp-total-mob">
                        {{ number_format($product->base_price) }}đ</div>
                </div>
                <div
                    class="flex items-center gap-1 border-2 border-[#1C1C1C] rounded-xl overflow-hidden shadow-[2px_2px_0px_#1C1C1C]">
                    <button onclick="changeQty(-1)"
                        class="w-9 h-9 flex items-center justify-center font-black text-lg hover:bg-gray-100">−</button>
                    <span class="font-black text-[#1C1C1C] min-w-[28px] text-center" id="pdp-qty-mob">1</span>
                    <button onclick="changeQty(1)"
                        class="w-9 h-9 flex items-center justify-center font-black text-lg bg-[#FF6B35] text-white">+</button>
                </div>
            </div>
            <button onclick="addToCartPDP()"
                class="w-full bg-[#FF6B35] text-white font-black py-3.5 rounded-xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-none transition-all flex items-center justify-center gap-2">
                🛒 Thêm vào giỏ hàng
            </button>
        </div>

    </div> {{-- Kết thúc #main-content --}}

    {{-- Toast Thông báo --}}
    <div id="toast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 hidden">
        <div
            class="bg-[#1C1C1C] text-white font-bold text-sm px-5 py-3 rounded-xl shadow-[4px_4px_0px_#FF6B35] flex items-center gap-2">
            ✅ Đã thêm vào giỏ hàng!
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const basePrice = {{ $product->base_price }};
        const productId = {{ $product->id }};

        // Tránh lỗi JS nếu sản phẩm không có options
        const optionValues = @json(
            $product->options
                ? $product->options->flatMap->values->map(fn($v) => ['id' => $v->id, 'extra_price' => $v->extra_price])->values()
                : []
        );
        let qty = 1;

        window.addEventListener('load', () => {
            document.getElementById('skeleton-screen').classList.add('hidden');
            document.getElementById('main-content').classList.remove('hidden');
        });

        function getSelectedOptions() {
            const options = [];
            document.querySelectorAll('input[type="radio"]:checked').forEach(el => options.push(parseInt(el.value)));
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(el => options.push(parseInt(el.value)));
            return options;
        }

        function updateOptionPrice() {
            const selected = getSelectedOptions();
            let extra = 0;
            optionValues.forEach(v => {
                if (selected.includes(v.id)) {
                    extra += parseInt(v.extra_price, 10) || 0;
                }
            });
            const total = (parseInt(basePrice, 10) + extra) * qty;
            const fmt = total.toLocaleString('vi-VN') + 'đ';
            document.getElementById('pdp-total-desk').textContent = fmt;
            document.getElementById('pdp-total-mob').textContent = fmt;
        }

        function changeQty(delta) {
            qty = Math.max(1, qty + delta);
            document.getElementById('pdp-qty-desk').textContent = qty;
            document.getElementById('pdp-qty-mob').textContent = qty;
            updateOptionPrice();
        }

        function addToCartPDP(isBuyNow = false) {
            const btns = document.querySelectorAll('[onclick^="addToCartPDP"]');
            btns.forEach(b => {
                b.disabled = true;
                b.textContent = '⏳ Đang thêm...';
            });

            const selectedOptions = getSelectedOptions();
            const note = document.getElementById('pdp-note')?.value || '';

            fetch('{{ route('client.cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: qty,
                    options: selectedOptions,
                    note: note
                })
            }).then(r => r.json()).then(data => {
                const toast = document.getElementById('toast');
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 2500);
                btns.forEach(b => {
                    b.disabled = false;
                    b.innerHTML = '🛒 Thêm vào giỏ hàng';
                });
            }).catch(() => {
                btns.forEach(b => {
                    b.disabled = false;
                    b.innerHTML = '🛒 Thêm vào giỏ hàng';
                });
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            });
        }
    </script>
@endpush
