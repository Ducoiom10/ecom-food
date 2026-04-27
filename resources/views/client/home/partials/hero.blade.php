{{-- Hero Banner --}}
<div class="relative overflow-hidden bg-[#1C1C1C] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#FF6B35] p-6 lg:p-8">
  <div class="relative z-10 max-w-sm">
    <div class="flex items-center gap-2 mb-3">
      <span class="bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-2 py-0.5 rounded-full border border-[#1C1C1C]">🔥 HOT DEAL</span>
      <span class="text-white/70 text-xs">Hôm nay thôi!</span>
    </div>
    <h2 class="text-white text-2xl lg:text-3xl font-black mb-2 leading-tight">Combo Trưa<br/>Văn Phòng 🏢</h2>
    <p class="text-white/70 text-sm mb-5">Mì trộn + Trà sữa chỉ còn <span class="text-[#FFD23F] font-black text-lg">65.000đ</span></p>
    <a href="{{ route('client.menu') }}" class="inline-flex items-center gap-2 bg-[#FF6B35] text-white font-black px-5 py-2.5 rounded-xl border-2 border-[#FF6B35] shadow-[2px_2px_0px_white] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
      Đặt ngay →
    </a>
  </div>
  <div class="absolute right-4 top-1/2 -translate-y-1/2 text-7xl lg:text-9xl opacity-10 select-none">🍜</div>
</div>

{{-- Group Order CTA --}}
@auth
<a href="{{ route('client.group-order') }}" class="block bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 lg:p-5 flex items-center gap-4 hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
  <div class="w-12 h-12 lg:w-14 lg:h-14 bg-[#1C1C1C] rounded-xl flex items-center justify-center flex-shrink-0">
    <span class="text-[#FFD23F] text-2xl lg:text-3xl">👥</span>
  </div>
  <div class="flex-1">
    <div class="font-black text-[#1C1C1C] text-base lg:text-lg flex items-center gap-2">
      Đặt đơn nhóm
      <span class="bg-[#FF6B35] text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">NEW</span>
    </div>
    <p class="text-xs lg:text-sm text-[#1C1C1C]/70 mt-0.5">Tạo phòng, gửi link, mỗi người tự chọn — chia bill tự động!</p>
  </div>
  <span class="text-[#1C1C1C] text-xl">›</span>
</a>
@endauth
