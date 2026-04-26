{{-- Combos sidebar (desktop) --}}
<div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden">
  <div class="bg-[#1C1C1C] px-4 py-3 flex items-center justify-between">
    <h3 class="text-white font-black text-sm">📈 Combo tiết kiệm</h3>
    <a href="{{ route('client.menu') }}" class="text-[#FFD23F] text-xs font-bold">Xem thêm ›</a>
  </div>
  <div class="p-4 space-y-3">
    @forelse($combos as $combo)
    <a href="{{ route('client.menu') }}" class="flex gap-3 hover:bg-orange-50 rounded-xl p-2 transition-all block">
      <img src="{{ $combo->image }}" alt="{{ $combo->name }}" class="w-16 h-16 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
      <div class="flex-1 min-w-0">
        <div class="font-black text-[#1C1C1C] text-sm">{{ $combo->name }}</div>
        <div class="text-xs text-gray-500 mt-0.5">{{ $combo->description }}</div>
        <div class="flex items-center gap-2 mt-1">
          <span class="font-black text-[#FF6B35] text-sm">{{ number_format($combo->combo_price) }}đ</span>
          <span class="text-xs text-gray-400 line-through">{{ number_format($combo->original_price) }}đ</span>
          <span class="bg-green-100 text-green-700 text-[10px] font-black px-1.5 py-0.5 rounded-full">
            -{{ number_format($combo->original_price - $combo->combo_price) }}đ
          </span>
        </div>
      </div>
    </a>
    @empty
    <p class="text-gray-400 text-sm text-center py-4">Chưa có combo nào.</p>
    @endforelse
  </div>
</div>
