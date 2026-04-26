@extends('layouts.admin')
@section('title', 'Smart Prep')
@section('page_title', 'Smart Prep')

@section('content')
<div class="h-full overflow-y-auto bg-[#0F0F0F] p-6">

  {{-- Alert Banner --}}
  @if(($criticalCount ?? 0) > 0 || ($highCount ?? 0) > 0)
  <div class="bg-red-600 border-2 border-red-400 rounded-2xl p-4 mb-6 flex items-center gap-3">
    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 text-xl">🔔</div>
    <div class="flex-1">
      <p class="text-white font-black">🚨 {{ $criticalCount ?? 0 }} cảnh báo KHẨN CẤP, {{ $highCount ?? 0 }} cảnh báo CAO!</p>
      <p class="text-red-200 text-xs mt-0.5">Hệ thống AI phát hiện cần chuẩn bị gấp trước giờ cao điểm</p>
    </div>
  </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Weather & Status --}}
    <div class="space-y-4">
      {{-- Weather Widget --}}
      <div class="bg-gradient-to-br from-[#1A2040] to-[#0D1520] border-2 border-[#2A3A6A] rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-4">
          <span class="text-blue-400">🧠</span>
          <span class="text-blue-300 text-xs font-black uppercase tracking-wide">Dữ liệu thời tiết</span>
        </div>
        <div class="flex items-center gap-4 mb-4">
          <span class="text-4xl">{{ $weather['icon'] ?? '🌧️' }}</span>
          <div>
            <div class="text-white font-black text-3xl">{{ $weather['temp'] ?? 24 }}°C</div>
            <div class="text-gray-400 text-sm">{{ $weather['label'] ?? 'Mưa lớn' }}</div>
          </div>
        </div>
        <div class="rounded-xl p-3 text-xs font-bold bg-blue-900/50 text-blue-300 border border-blue-700/50">
          {{ $weather['impact'] ?? 'Mưa lớn → Đơn ship tăng vọt 85%!' }}
        </div>
        @if(($weather['deliveryBoost'] ?? 0) > 0)
        <div class="mt-3 flex items-center gap-2 bg-green-900/30 border border-green-700/30 rounded-xl p-3">
          <span class="text-green-400">📈</span>
          <div>
            <div class="text-green-400 font-black text-sm">Đơn Ship +{{ $weather['deliveryBoost'] }}%</div>
            <div class="text-green-300/70 text-[10px]">So với ngày nắng bình thường</div>
          </div>
        </div>
        @endif

        {{-- Weather selector --}}
        <div class="mt-3">
          <p class="text-gray-500 text-[10px] mb-1.5">Mô phỏng thời tiết:</p>
          <div class="flex gap-1">
            @foreach([['id'=>'sunny','icon'=>'☀️'],['id'=>'cloudy','icon'=>'☁️'],['id'=>'rainy','icon'=>'🌧️'],['id'=>'stormy','icon'=>'⛈️']] as $w)
            <a href="{{ route('admin.smartprep', ['weather' => $w['id']]) }}"
              class="flex-1 py-1 rounded-lg text-center text-lg transition-all {{ ($currentWeather ?? 'rainy') === $w['id'] ? 'bg-blue-500 border border-blue-400' : 'bg-[#252525] border border-[#333] hover:border-blue-500/50' }}">
              {{ $w['icon'] }}
            </a>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Meal period --}}
      <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-3">
          <span class="text-[#FF6B35]">🕐</span>
          <span class="text-white font-black text-sm">Ca hiện tại</span>
        </div>
        <div class="flex items-center gap-3 {{ ($mealPeriod['peak'] ?? false) ? 'text-[#FFD23F]' : 'text-gray-300' }}">
          <span class="text-3xl">{{ $mealPeriod['emoji'] ?? '☀️' }}</span>
          <div>
            <div class="font-black text-lg">{{ $mealPeriod['name'] ?? 'Bữa trưa' }}</div>
            @if($mealPeriod['peak'] ?? false)
            <div class="text-xs text-[#FF6B35] font-bold">⚡ GIỜ CAO ĐIỂM!</div>
            @endif
          </div>
        </div>
      </div>

      {{-- Stats --}}
      <div class="grid grid-cols-2 gap-3">
        <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4 text-center">
          <div class="text-[#FFD23F] font-black text-2xl">{{ $pendingRecs ?? 0 }}</div>
          <div class="text-gray-400 text-xs mt-1">Gợi ý chờ</div>
        </div>
        <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4 text-center">
          <div class="text-green-400 font-black text-2xl">{{ $acknowledgedRecs ?? 0 }}</div>
          <div class="text-gray-400 text-xs mt-1">Đã xử lý</div>
        </div>
      </div>

      <a href="{{ route('admin.smartprep', array_merge(request()->all(), ['refresh' => 1])) }}"
        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-black py-3 rounded-2xl border-2 border-purple-500 hover:from-purple-500 hover:to-blue-500 transition-all block text-center">
        🧠 Cập nhật dự báo AI
      </a>
    </div>

    {{-- Center: Recommendations --}}
    <div class="lg:col-span-2 space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-white font-black text-xl flex items-center gap-2">🧠 Gợi ý chuẩn bị từ AI</h2>
        <span class="text-gray-500 text-xs bg-[#1A1A1A] border border-[#333] px-3 py-1.5 rounded-lg">Dựa trên lịch sử 30 ngày + thời tiết</span>
      </div>

      @foreach($recommendations ?? [] as $rec)
      @php
        $urgencyConfig = [
          'critical' => ['bg' => 'bg-red-900/30',    'border' => 'border-red-500',    'badge' => 'bg-red-500 text-white',    'label' => 'KHẨN CẤP'],
          'high'     => ['bg' => 'bg-orange-900/30',  'border' => 'border-orange-500',  'badge' => 'bg-orange-500 text-white',  'label' => 'CAO'],
          'medium'   => ['bg' => 'bg-yellow-900/20',  'border' => 'border-yellow-500',  'badge' => 'bg-yellow-500 text-black',  'label' => 'TRUNG BÌNH'],
          'low'      => ['bg' => 'bg-[#1A1A1A]',      'border' => 'border-[#333]',      'badge' => 'bg-gray-600 text-white',    'label' => 'THẤP'],
        ];
        $cfg = $urgencyConfig[$rec['urgency']] ?? $urgencyConfig['low'];
        $progress = min(100, ($rec['prepped'] / max(1, $rec['predicted'])) * 100);
      @endphp
      <div class="border-2 rounded-2xl overflow-hidden transition-all {{ $cfg['border'] }} {{ $cfg['bg'] }}">
        <div class="p-4">
          <div class="flex items-start gap-3 mb-3">
            <span class="text-3xl flex-shrink-0">{{ $rec['emoji'] }}</span>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="text-white font-black">{{ $rec['item'] }}</span>
                <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $cfg['badge'] }}">{{ $cfg['label'] }}</span>
              </div>
              <p class="text-gray-300 text-xs">{{ $rec['reason'] }}</p>
            </div>
          </div>

          {{-- Progress --}}
          <div class="bg-black/30 rounded-xl p-3 mb-3">
            <div class="flex items-center justify-between text-xs mb-2">
              <span class="text-gray-400">Đã chuẩn bị</span>
              <span class="text-gray-400">Dự báo cần</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-white font-black text-lg">{{ $rec['prepped'] }}</span>
              <div class="flex-1 h-3 bg-[#333] rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all {{ $progress >= 80 ? 'bg-green-500' : ($progress >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $progress }}%"></div>
              </div>
              <span class="text-[#FFD23F] font-black text-lg">{{ $rec['predicted'] }}</span>
            </div>
            <div class="flex justify-between text-[10px] text-gray-500 mt-1">
              <span>{{ $rec['unit'] }} đã có</span>
              <span class="{{ $progress < 70 ? 'text-red-400 font-bold' : 'text-green-400' }}">
                {{ $progress < 100 ? 'Thiếu ' . ($rec['predicted'] - $rec['prepped']) . ' ' . $rec['unit'] : 'Đủ!' }}
              </span>
            </div>
          </div>

          {{-- Action --}}
          <div class="flex gap-2 items-center">
            <div class="flex-1 bg-black/30 border border-[#444] rounded-xl px-3 py-2 text-xs font-bold text-[#FFD23F]">
              👉 {{ $rec['action'] }}
            </div>
            <form action="{{ route('admin.smartprep.acknowledge', $rec['id']) }}" method="POST">
              @csrf
              <button type="submit" class="flex items-center gap-1.5 bg-green-600 text-white text-xs font-black px-3 py-2 rounded-xl border border-green-500 whitespace-nowrap hover:bg-green-500 transition-all">
                ✓ Đã làm
              </button>
            </form>
          </div>
        </div>
      </div>
      @endforeach

    </div>
  </div>
</div>
@endsection
