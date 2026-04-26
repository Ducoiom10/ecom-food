{{-- Product card tái sử dụng --}}
{{-- Nhận $product (Eloquent model hoặc array) --}}
@php
  $id    = $product->id    ?? $product['id'];
  $name  = $product->name  ?? $product['name'];
  $price = $product->base_price ?? $product['base_price'] ?? $product['price'] ?? 0;
  $image = $product->image ?? $product['image'] ?? '';
  $isNew = $product->is_new ?? $product['isNew'] ?? false;
  $isTop = $product->is_best_seller ?? $product['isBestSeller'] ?? false;
  $cat   = $product->category->name ?? $product['category'] ?? '';
@endphp

<div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all group">
  <a href="{{ route('client.product', $id) }}" class="block">
    <div class="relative overflow-hidden aspect-[4/3]">
      <img src="{{ $image }}" alt="{{ $name }}"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
      @if($isNew)
      <div class="absolute top-2 left-2 bg-[#FFD23F] border border-[#1C1C1C] text-[#1C1C1C] text-[9px] font-black px-1.5 py-0.5 rounded-full">✨ MỚI</div>
      @endif
      @if($isTop)
      <div class="absolute top-2 right-2 bg-[#FF6B35] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">🔥 TOP</div>
      @endif
    </div>
    <div class="p-3">
      <div class="font-black text-[#1C1C1C] text-sm leading-tight line-clamp-2">{{ $name }}</div>
      @if($cat)
      <div class="text-xs text-gray-400 mt-1">{{ $cat }}</div>
      @endif
    </div>
  </a>
  <div class="px-3 pb-3 flex items-center justify-between">
    <span class="font-black text-[#FF6B35]">{{ number_format($price) }}đ</span>
    @auth
    <button onclick="addToCart({{ $id }})"
      class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-[#FFD23F] flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all text-lg font-bold">+</button>
    @else
    <a href="{{ route('login') }}"
      class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-[#FFD23F] flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all text-lg font-bold">+</a>
    @endauth
  </div>
</div>
