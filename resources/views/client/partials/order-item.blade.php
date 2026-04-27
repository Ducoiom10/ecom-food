{{-- Order item row tái sử dụng --}}
{{-- Nhận $item (OrderItem model) --}}
<div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
  <img src="{{ $item->product->image ?? '' }}" alt="{{ $item->product->name ?? '' }}"
    class="w-12 h-12 object-cover rounded-xl border border-gray-200 flex-shrink-0" />
  <div class="flex-1 min-w-0">
    <div class="font-bold text-sm text-[#1C1C1C] truncate">{{ $item->product->name ?? '—' }}</div>
    @if($item->note)
    <div class="text-xs text-orange-500 mt-0.5">📝 {{ $item->note }}</div>
    @endif
    <div class="text-xs text-gray-400 mt-0.5">x{{ $item->quantity }}</div>
  </div>
  <div class="text-right flex-shrink-0">
    <div class="font-black text-[#FF6B35] text-sm">{{ number_format($item->price * $item->quantity) }}đ</div>
    <div class="text-xs text-gray-400">{{ number_format($item->price) }}đ/món</div>
  </div>
</div>
