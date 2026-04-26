{{-- Combos mobile --}}
<div>
  <div class="flex items-center justify-between mb-3">
    <h3 class="font-black text-[#1C1C1C] flex items-center gap-2">📈 Combo tiết kiệm</h3>
    <a href="{{ route('client.menu') }}" class="text-[#FF6B35] text-xs font-bold">Xem thêm ›</a>
  </div>
  <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
    @foreach($combos as $combo)
    <a href="{{ route('client.menu') }}" class="flex-shrink-0 w-48 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden block">
      <div class="h-24 overflow-hidden">
        <img src="{{ $combo->image }}" alt="{{ $combo->name }}" class="w-full h-full object-cover" />
      </div>
      <div class="p-3">
        <div class="font-black text-[#1C1C1C] text-xs">{{ $combo->name }}</div>
        <div class="flex items-center gap-1 mt-1">
          <span class="font-black text-[#FF6B35] text-sm">{{ number_format($combo->combo_price) }}đ</span>
          <span class="text-[10px] text-gray-400 line-through">{{ number_format($combo->original_price) }}đ</span>
        </div>
      </div>
    </a>
    @endforeach
  </div>
</div>
