@extends('layouts.client')
@section('title', 'Tài khoản')
@section('page_heading', 'Tài khoản')

@section('content')
<div class="pb-4">

  {{-- Profile header --}}
  <div class="bg-[#1C1C1C] px-4 pt-6 pb-8 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 bg-[#FF6B35]/20 rounded-full -translate-x-4 -translate-y-4"></div>
    <div class="relative z-10 flex items-center gap-4">
      <div class="w-16 h-16 bg-[#FF6B35] border-2 border-[#FFD23F] rounded-2xl flex items-center justify-center text-2xl font-black text-white shadow-[4px_4px_0px_#FFD23F]">
        {{ strtoupper(substr($user->name, 0, 1)) }}
      </div>
      <div>
        <h2 class="text-white font-black text-xl">{{ $user->name }}</h2>
        <p class="text-gray-400 text-sm">{{ $user->phone }}</p>
        <div class="flex items-center gap-2 mt-1">
          <span class="bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-2 py-0.5 rounded-full">
            🏆 {{ ucfirst($user->tier ?? 'bronze') }}
          </span>
          <span class="text-gray-500 text-xs">Thành viên từ {{ $user->created_at->format('m/Y') }}</span>
        </div>
      </div>
    </div>
    {{-- Edit profile link --}}
    <a href="{{ route('client.profile.edit') }}" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xs font-bold border border-gray-600 px-2 py-1 rounded-lg transition-colors">
      ✏️ Sửa
    </a>
  </div>

  {{-- Snack Points --}}
  <div class="mx-4 -mt-4 relative z-10">
    <div class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 flex items-center gap-4">
      <div class="w-12 h-12 bg-[#1C1C1C] rounded-xl flex items-center justify-center flex-shrink-0">
        <span class="text-[#FFD23F] text-2xl">⭐</span>
      </div>
      <div class="flex-1">
        <p class="text-[#1C1C1C]/60 text-xs font-bold uppercase">Snack Points</p>
        <p class="font-black text-[#1C1C1C] text-3xl">{{ number_format($snackPoints) }}</p>
        <p class="text-[#1C1C1C]/70 text-xs mt-0.5">≈ {{ number_format($snackPoints * 100) }}đ</p>
      </div>
      <div class="text-right">
        <span class="text-xs font-black text-green-700">📈 Tích điểm mỗi đơn</span>
      </div>
    </div>
  </div>

  {{-- Tabs --}}
  <div class="flex px-4 mt-4 gap-2">
    <button onclick="switchTab('orders')" id="tab-orders"
      class="flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-black bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]">
      🧾 Lịch sử đơn
    </button>
    <button onclick="switchTab('loyalty')" id="tab-loyalty"
      class="flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-black bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">
      ⭐ Phần thưởng
    </button>
  </div>

  {{-- Orders tab --}}
  <div id="panel-orders" class="px-4 mt-4 space-y-3">
    @forelse($orderHistory as $order)
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
      <div class="flex items-center justify-between mb-2">
        <div>
          <span class="font-black text-[#1C1C1C] text-sm">{{ $order->order_number }}</span>
          <div class="text-gray-400 text-xs mt-0.5">🕐 {{ $order->created_at->format('d/m/Y') }}</div>
        </div>
        <span class="text-xs font-black px-2 py-0.5 rounded-full border
          {{ $order->status === 'completed' ? 'bg-green-100 text-green-600 border-green-200' : 'bg-orange-100 text-orange-600 border-orange-200' }}">
          {{ $order->status === 'completed' ? '✓ Hoàn thành' : ucfirst($order->status) }}
        </span>
      </div>
      <div class="text-xs text-gray-500 mb-3">
        {{ $order->items->map(fn($i) => $i->product->name . ' x' . $i->quantity)->join(' · ') }}
      </div>
      <div class="flex items-center justify-between">
        <span class="font-black text-[#FF6B35]">{{ number_format($order->grand_total) }}đ</span>
        <div class="flex gap-2">
          <a href="{{ route('client.order.show', $order->id) }}" class="text-xs font-bold text-gray-500 border border-gray-200 px-2 py-1 rounded-lg hover:border-gray-400 transition-colors">
            Chi tiết
          </a>
          <button class="flex items-center gap-1.5 bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-3 py-2 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all">
            🔄 Đặt lại
          </button>
        </div>
      </div>
    </div>
    @empty
    <div class="text-center py-12 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C]">
      <div class="text-5xl mb-3">🧾</div>
      <p class="font-black text-[#1C1C1C]">Chưa có đơn hàng nào</p>
      <a href="{{ route('client.menu') }}" class="mt-3 inline-block text-[#FF6B35] font-bold text-sm hover:underline">Đặt món ngay →</a>
    </div>
    @endforelse
  </div>

  {{-- Loyalty tab --}}
  <div id="panel-loyalty" class="px-4 mt-4 space-y-3 hidden">
    @forelse($challenges as $challenge)
    @php $progress = $progressMap[$challenge->id] ?? null; $current = $progress?->current_count ?? 0; $completed = $progress?->completed_at !== null; @endphp
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 {{ $completed ? 'opacity-70' : '' }}">
      <div class="flex items-center gap-3">
        <span class="text-2xl">{{ ['order_streak'=>'🎯','lunch_order'=>'☀️','try_new'=>'✨','referral'=>'👥'][$challenge->type] ?? '🏆' }}</span>
        <div class="flex-1">
          <div class="flex items-center justify-between">
            <span class="font-black text-[#1C1C1C] text-sm">{{ $challenge->title }}</span>
            <span class="text-[#FFD23F] font-black text-sm">+{{ $challenge->points_reward }} pts</span>
          </div>
          @if($challenge->description)
          <p class="text-xs text-gray-500 mt-0.5">{{ $challenge->description }}</p>
          @endif
          <div class="flex items-center gap-2 mt-1.5">
            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
              <div class="h-full bg-[#FF6B35] rounded-full transition-all"
                style="width: {{ min(100, ($current / max(1, $challenge->target_count)) * 100) }}%"></div>
            </div>
            <span class="text-xs text-gray-500">{{ $current }}/{{ $challenge->target_count }}</span>
          </div>
        </div>
      </div>
      @if($completed)
      <div class="mt-2 bg-green-50 border border-green-200 rounded-xl px-3 py-1.5 text-xs text-green-600 font-bold text-center">✓ Đã hoàn thành!</div>
      @endif
    </div>
    @empty
    <div class="text-center py-12 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C]">
      <div class="text-5xl mb-3">⭐</div>
      <p class="font-black text-[#1C1C1C]">Chưa có thử thách nào</p>
    </div>
    @endforelse
  </div>

  {{-- Settings --}}
  <div class="px-4 mt-6 space-y-2">
    @foreach([
      ['icon'=>'✏️', 'label'=>'Chỉnh sửa hồ sơ',  'sub'=>'Tên, email, số điện thoại',  'route'=>'client.profile.edit'],
      ['icon'=>'🔒', 'label'=>'Đổi mật khẩu',      'sub'=>'Cập nhật mật khẩu bảo mật', 'route'=>'client.profile.password'],
      ['icon'=>'🔔', 'label'=>'Thông báo',          'sub'=>'Email & Push notification',  'route'=>'client.profile'],
      ['icon'=>'❓', 'label'=>'Trợ giúp',           'sub'=>'FAQ, Báo cáo sự cố',         'route'=>'client.profile'],
    ] as $setting)
    <a href="{{ route($setting['route']) }}"
      class="w-full bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] px-4 py-3 flex items-center gap-3 hover:shadow-none hover:translate-x-[1px] hover:translate-y-[1px] transition-all block">
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
    if (t === tab) {
      btn.classList.add('bg-[#FF6B35]','text-white');
      btn.classList.remove('bg-white','text-[#1C1C1C]');
    } else {
      btn.classList.remove('bg-[#FF6B35]','text-white');
      btn.classList.add('bg-white','text-[#1C1C1C]');
    }
  });
}
</script>
@endpush
