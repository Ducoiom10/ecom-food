{{-- User stats (desktop, chỉ hiện khi đã login) --}}
@auth
<div class="bg-[#1C1C1C] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#FF6B35] p-4">
  <h3 class="text-white font-black mb-3 text-sm">👤 Tài khoản của bạn</h3>
  <div class="flex items-center gap-3 mb-3">
    <div class="w-10 h-10 bg-[#FF6B35] rounded-xl flex items-center justify-center text-white font-black">
      {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </div>
    <div>
      <div class="text-white font-black text-sm">{{ auth()->user()->name }}</div>
      <div class="text-gray-400 text-xs">{{ ucfirst(auth()->user()->tier ?? 'bronze') }}</div>
    </div>
  </div>
  <div class="bg-[#FFD23F] rounded-xl p-3 flex items-center justify-between">
    <div>
      <div class="text-[#1C1C1C] text-xs font-bold">Snack Points</div>
      <div class="text-[#1C1C1C] font-black text-xl">{{ number_format(auth()->user()->snack_points ?? 0) }}</div>
    </div>
    <span class="text-2xl">⭐</span>
  </div>
</div>
@endauth
