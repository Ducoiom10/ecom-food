{{-- Reviews sidebar (desktop) --}}
<div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden">
  <div class="bg-[#1C1C1C] px-4 py-3">
    <h3 class="text-white font-black text-sm">⭐ Khách hàng nói gì?</h3>
  </div>
  <div class="p-4 space-y-3">
    @foreach($reviews as $review)
    <div class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
      <div class="flex items-center gap-2 mb-1.5">
        <div class="w-7 h-7 bg-[#FF6B35] rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0">
          {{ strtoupper(substr($review['user'], 0, 2)) }}
        </div>
        <div>
          <div class="font-bold text-xs text-[#1C1C1C]">{{ $review['user'] }}</div>
          <div class="text-[#FFD23F] text-xs">{{ str_repeat('⭐', $review['rating']) }}</div>
        </div>
      </div>
      <p class="text-xs text-gray-600 leading-relaxed">"{{ $review['comment'] }}"</p>
      <div class="text-[10px] text-gray-400 mt-1">{{ $review['item'] }} · {{ $review['time'] }}</div>
    </div>
    @endforeach
  </div>
</div>
