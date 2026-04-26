@extends('layouts.client')
@section('title', 'Tài khoản')

@section('content')
<div class="pb-4">

  {{-- Profile header --}}
  <div class="bg-[#1C1C1C] px-4 pt-6 pb-8 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 bg-[#FF6B35]/20 rounded-full -translate-x-4 -translate-y-4"></div>
    <div class="relative z-10 flex items-center gap-4">
      <div class="w-16 h-16 bg-[#FF6B35] border-2 border-[#FFD23F] rounded-2xl flex items-center justify-center text-2xl shadow-[4px_4px_0px_#FFD23F]">👤</div>
      <div>
        <h2 class="text-white font-black text-xl">{{ $user['name'] ?? 'Minh Tuấn' }}</h2>
        <p class="text-gray-400 text-sm">{{ $user['email'] ?? 'minhtuan@email.com' }}</p>
        <div class="flex items-center gap-2 mt-1">
          <span class="bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-2 py-0.5 rounded-full">🏆 VIP Gold</span>
          <span class="text-gray-500 text-xs">Thành viên từ 01/2026</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Snack Points --}}
  <div class="mx-4 -mt-4 relative z-10">
    <div class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 flex items-center gap-4">
      <div class="w-12 h-12 bg-[#1C1C1C] rounded-xl flex items-center justify-center flex-shrink-0">
        <span class="text-[#FFD23F] text-2xl">⭐</span>
      </div>
      <div class="flex-1">
        <p class="text-[#1C1C1C]/60 text-xs font-bold uppercase">Snack Points</p>
        <p class="font-black text-[#1C1C1C] text-3xl">{{ number_format($snackPoints ?? 342) }}</p>
        <p class="text-[#1C1C1C]/70 text-xs mt-0.5">≈ {{ number_format(($snackPoints ?? 342) * 100) }}đ · Đủ dùng 1 bữa miễn phí!</p>
      </div>
      <div class="text-right">
        <span class="text-xs font-black text-green-700">📈 +42 tuần này</span>
      </div>
    </div>
  </div>

  {{-- Tabs --}}
  <div class="flex px-4 mt-4 gap-2">
    <button onclick="switchTab('orders')" id="tab-orders" class="flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-black bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]">🧾 Lịch sử đơn</button>
    <button onclick="switchTab('loyalty')" id="tab-loyalty" class="flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-black bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">⭐ Phần thưởng</button>
  </div>

  {{-- Orders tab --}}
  <div id="panel-orders" class="px-4 mt-4 space-y-3">
    @foreach($orderHistory ?? [] as $order)
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
      <div class="flex items-center justify-between mb-2">
        <div>
          <span class="font-black text-[#1C1C1C] text-sm">{{ $order['id'] }}</span>
          <div class="text-gray-400 text-xs mt-0.5">🕐 {{ $order['date'] }}</div>
        </div>
        <span class="bg-green-100 text-green-600 text-xs font-black px-2 py-0.5 rounded-full border border-green-200">✓ Hoàn thành</span>
      </div>
      <div class="text-xs text-gray-500 mb-3">{{ implode(' · ', $order['items']) }}</div>
      <div class="flex items-center justify-between">
        <span class="font-black text-[#FF6B35]">{{ number_format($order['total']) }}đ</span>
        <button class="flex items-center gap-1.5 bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-3 py-2 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">
          🔄 Đặt lại
        </button>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Loyalty tab --}}
  <div id="panel-loyalty" class="px-4 mt-4 space-y-3 hidden">
    @foreach([
      ['title' => 'Mua 5 đơn liên tiếp',       'points' => 50,  'progress' => 3, 'max' => 5, 'emoji' => '🎯'],
      ['title' => 'Đặt vào giờ trưa tuần này',  'points' => 30,  'progress' => 4, 'max' => 5, 'emoji' => '☀️'],
      ['title' => 'Thử thực đơn mới',            'points' => 20,  'progress' => 1, 'max' => 1, 'emoji' => '✨', 'completed' => true],
      ['title' => 'Giới thiệu bạn bè',           'points' => 100, 'progress' => 0, 'max' => 1, 'emoji' => '👥'],
    ] as $challenge)
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 {{ isset($challenge['completed']) ? 'opacity-70' : '' }}">
      <div class="flex items-center gap-3">
        <span class="text-2xl">{{ $challenge['emoji'] }}</span>
        <div class="flex-1">
          <div class="flex items-center justify-between">
            <span class="font-black text-[#1C1C1C] text-sm">{{ $challenge['title'] }}</span>
            <span class="text-[#FFD23F] font-black text-sm">+{{ $challenge['points'] }} pts</span>
          </div>
          <div class="flex items-center gap-2 mt-1.5">
            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
              <div class="h-full bg-[#FF6B35] rounded-full" style="width: {{ ($challenge['progress'] / $challenge['max']) * 100 }}%"></div>
            </div>
            <span class="text-xs text-gray-500">{{ $challenge['progress'] }}/{{ $challenge['max'] }}</span>
          </div>
        </div>
      </div>
      @if(isset($challenge['completed']))
      <div class="mt-2 bg-green-50 border border-green-200 rounded-xl px-3 py-1.5 text-xs text-green-600 font-bold text-center">✓ Đã hoàn thành!</div>
      @endif
    </div>
    @endforeach
  </div>

  {{-- Settings --}}
  <div class="px-4 mt-6 space-y-2">
    @foreach([
      ['icon' => '🔔', 'label' => 'Thông báo',  'sub' => 'Email & Push notification', 'route' => '#'],
      ['icon' => '🔒', 'label' => 'Bảo mật',    'sub' => 'Đổi mật khẩu, 2FA',         'route' => '#'],
      ['icon' => '❓', 'label' => 'Trợ giúp',   'sub' => 'FAQ, Báo cáo sự cố',        'route' => '#'],
    ] as $setting)
    <a href="{{ $setting['route'] }}" class="w-full bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] px-4 py-3 flex items-center gap-3 hover:shadow-[1px_1px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all block">
      <div class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0 text-lg">{{ $setting['icon'] }}</div>
      <div class="flex-1 text-left">
        <div class="font-black text-[#1C1C1C] text-sm">{{ $setting['label'] }}</div>
        <div class="text-xs text-gray-400">{{ $setting['sub'] }}</div>
      </div>
      <span class="text-gray-400">›</span>
    </a>
    @endforeach
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="w-full bg-red-50 border-2 border-red-200 rounded-2xl px-4 py-3 flex items-center gap-3 hover:bg-red-100 transition-all">
        <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0 text-lg">🚪</div>
        <span class="font-black text-red-500 text-sm">Đăng xuất</span>
      </button>
    </form>
  </div>

</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
  ['orders','loyalty'].forEach(t => {
    document.getElementById('panel-' + t).classList.toggle('hidden', t !== tab);
    const btn = document.getElementById('tab-' + t);
    if (t === tab) btn.className = btn.className.replace('bg-white text-[#1C1C1C]', 'bg-[#FF6B35] text-white');
    else btn.className = btn.className.replace('bg-[#FF6B35] text-white', 'bg-white text-[#1C1C1C]');
  });
}
</script>
@endpush
