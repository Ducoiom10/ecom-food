{{-- Reviews mobile --}}
<div>
  <h3 class="font-black text-[#1C1C1C] mb-3">⭐ Khách hàng nói gì?</h3>
  <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
    @foreach($reviews as $review)
    <div class="flex-shrink-0 w-64 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3">
      <div class="flex items-center gap-2 mb-2">
        <div class="w-8 h-8 bg-[#FF6B35] rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0">
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
