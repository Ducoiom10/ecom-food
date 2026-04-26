@extends('layouts.admin')
@section('title', 'Điều phối')
@section('page_title', 'Điều phối')

@section('content')
<div class="h-full flex flex-col lg:flex-row overflow-hidden bg-[#0F0F0F]">

  {{-- Map --}}
  <div class="hidden lg:block flex-1 relative overflow-hidden bg-[#111]">
    <div class="absolute inset-0 opacity-20">
      <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
        <defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="#333" stroke-width="0.5"/></pattern></defs>
        <rect width="100%" height="100%" fill="url(#grid)"/>
        <line x1="0" y1="200" x2="100%" y2="200" stroke="#555" stroke-width="3"/>
        <line x1="0" y1="400" x2="100%" y2="400" stroke="#555" stroke-width="2"/>
        <line x1="200" y1="0" x2="200" y2="100%" stroke="#555" stroke-width="3"/>
        <line x1="450" y1="0" x2="450" y2="100%" stroke="#555" stroke-width="2"/>
      </svg>
    </div>
    <div class="absolute top-4 left-4 right-4 flex items-center justify-between z-10">
      <div class="bg-[#1A1A1A]/90 backdrop-blur border border-[#333] rounded-xl px-4 py-2 flex items-center gap-2">
        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
        <span class="text-white text-sm font-black">Live Dispatch Map</span>
      </div>
      <div class="bg-[#1A1A1A]/90 backdrop-blur border border-[#333] rounded-xl px-3 py-2 text-xs text-gray-400">🔄 Cập nhật mỗi 10s</div>
    </div>
    @foreach($shippers as $s)
    <div class="absolute flex flex-col items-center transform -translate-x-1/2 -translate-y-1/2"
         style="left:{{ 20 + ($loop->index * 15) }}%;top:{{ 30 + ($loop->index * 12) }}%">
      <div class="w-10 h-10 rounded-full border-2 border-white flex items-center justify-center font-black text-white shadow-xl {{ $s->isFree() ? 'bg-green-500' : 'bg-[#FF6B35]' }}">
        {{ strtoupper(substr($s->name, 0, 1)) }}
      </div>
      @if(!$s->isFree())
      <div class="mt-1 bg-[#1A1A1A] border border-[#444] text-white text-[9px] font-bold px-2 py-0.5 rounded-full">{{ $s->active_order_count }} đơn</div>
      @endif
      <div class="mt-0.5 w-2 h-2 rounded-full animate-ping {{ $s->isFree() ? 'bg-green-500' : 'bg-orange-500' }}"></div>
    </div>
    @endforeach
    <div class="absolute" style="left:55%;top:50%">
      <div class="w-8 h-8 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center shadow-xl text-lg transform -translate-x-1/2 -translate-y-1/2">🍜</div>
    </div>
    @if($batchableCount >= 2)
    <div class="absolute bottom-4 left-4 bg-[#FFD23F]/90 border-2 border-[#1C1C1C] rounded-xl px-4 py-3 max-w-xs">
      <div class="flex items-center gap-2 mb-1"><span>👥</span><span class="font-black text-[#1C1C1C] text-sm">Gom đơn thông minh</span></div>
      <p class="text-xs text-[#1C1C1C]/80">{{ $batchableCount }} đơn sẵn sàng · Có thể gom 1 chuyến!</p>
    </div>
    @endif
  </div>

  {{-- Orders panel --}}
  <div class="flex-1 lg:flex-none lg:w-96 xl:w-[420px] flex-shrink-0 bg-[#1A1A1A] lg:border-l-2 border-[#333] flex flex-col overflow-hidden">

    {{-- Stats --}}
    <div class="grid grid-cols-3 border-b-2 border-[#333]">
      <div class="px-4 py-3 text-center border-r border-[#333]">
        <div class="font-black text-lg text-orange-400">{{ $activeCount }}</div>
        <div class="text-gray-500 text-[10px]">Đang active</div>
      </div>
      <div class="px-4 py-3 text-center border-r border-[#333]">
        <div class="font-black text-lg text-green-400">{{ $freeShippers }} rảnh</div>
        <div class="text-gray-500 text-[10px]">Shipper</div>
      </div>
      <div class="px-4 py-3 text-center">
        <div class="font-black text-lg text-blue-400">{{ $todayCount }}</div>
        <div class="text-gray-500 text-[10px]">Hôm nay</div>
      </div>
    </div>

    {{-- Mobile map toggle --}}
    <div class="lg:hidden px-4 py-2 border-b border-[#333]">
      <button onclick="toggleMap()" class="w-full text-xs font-bold text-gray-400 hover:text-white py-1.5 flex items-center justify-center gap-2">
        🗺️ Xem bản đồ
      </button>
    </div>
    <div id="mobile-map" class="lg:hidden hidden h-48 relative bg-[#111] border-b border-[#333]">
      <div class="absolute inset-0 opacity-20">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
          <defs><pattern id="grid2" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="#333" stroke-width="0.5"/></pattern></defs>
          <rect width="100%" height="100%" fill="url(#grid2)"/>
        </svg>
      </div>
      <div class="absolute" style="left:55%;top:50%">
        <div class="w-8 h-8 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center text-lg transform -translate-x-1/2 -translate-y-1/2">🍜</div>
      </div>
    </div>

    {{-- Order list --}}
    <div class="flex-1 overflow-y-auto">
      @forelse($orders as $order)
      @php
        $cfg = match($order->status) {
          'ready'      => ['label' => 'Chờ giao',  'color' => 'bg-yellow-500 text-black'],
          'delivering' => ['label' => 'Đang giao', 'color' => 'bg-blue-500 text-white'],
          default      => ['label' => $order->status, 'color' => 'bg-gray-500 text-white'],
        };
      @endphp
      <div class="border-b border-[#222] p-4 hover:bg-[#1D1D1D] cursor-pointer transition-all" onclick="toggleOrder({{ $order->id }})">
        <div class="flex items-start gap-3">
          <div class="w-8 h-8 bg-[#FF6B35] rounded-xl flex items-center justify-center flex-shrink-0 text-sm">📦</div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-1">
              <span class="text-white font-black text-sm">{{ $order->order_number }}</span>
              <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $cfg['color'] }}">{{ $cfg['label'] }}</span>
            </div>
            <div class="text-gray-400 text-xs truncate">{{ $order->user?->name ?? 'Khách' }}</div>
            <div class="text-gray-500 text-[10px] mt-0.5 truncate">📍 {{ $order->delivery_address ?? 'Không có địa chỉ' }}</div>
            <div class="flex items-center justify-between mt-2 text-xs text-gray-400">
              <span>🚚 {{ $order->shipper?->name ?? 'Chưa phân công' }}</span>
              <span>⏱ {{ $order->estimated_eta ? \Carbon\Carbon::parse($order->estimated_eta)->format('H:i') : '--:--' }}</span>
            </div>
          </div>
        </div>
        <div id="order-{{ $order->id }}" class="mt-3 pt-3 border-t border-[#333] space-y-2 hidden">
          <div class="flex gap-2">
            @if(!$order->shipper_id)
            <button onclick="openAssign({{ $order->id }})" class="flex-1 text-xs font-bold bg-[#FFD23F] text-[#1C1C1C] py-2 rounded-xl border border-[#444] flex items-center justify-center gap-1">
              🚚 Phân shipper
            </button>
            @endif
            <form action="{{ route('admin.dispatch.update', $order->id) }}" method="POST" class="flex-1">
              @csrf @method('PATCH')
              <button type="submit" class="w-full text-xs font-bold bg-[#FF6B35] text-white py-2 rounded-xl flex items-center justify-center gap-1">✓ Cập nhật</button>
            </form>
          </div>
          @if($order->shipper)
          <a href="tel:{{ $order->shipper->phone }}" class="flex items-center gap-2 text-xs text-blue-400">
            📞 Gọi {{ $order->shipper->name }}: {{ $order->shipper->phone }}
          </a>
          @endif
        </div>
      </div>
      @empty
      <div class="flex flex-col items-center justify-center h-48 text-gray-500">
        <span class="text-4xl mb-2">📭</span>
        <p class="text-sm">Không có đơn nào đang giao</p>
      </div>
      @endforelse
    </div>
  </div>

  {{-- Assign modal --}}
  <div id="assign-modal" class="fixed inset-0 bg-black/70 z-50 items-center justify-center p-4 hidden" onclick="closeAssign(event)">
    <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-5 w-full max-w-sm shadow-2xl">
      <h3 class="text-white font-black text-lg mb-4">Phân công Shipper</h3>
      <div class="space-y-2 mb-4">
        @foreach($shippers as $s)
        <form action="{{ route('admin.dispatch.assign') }}" method="POST">
          @csrf
          <input type="hidden" name="order_id" class="assign-order-id" value="" />
          <input type="hidden" name="shipper_id" value="{{ $s->id }}" />
          <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl border transition-all {{ $s->isFree() ? 'border-green-500/50 hover:border-green-500 hover:bg-green-900/20' : 'border-[#333] opacity-60' }}">
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-white {{ $s->isFree() ? 'bg-green-500' : 'bg-gray-600' }}">
              {{ strtoupper(substr($s->name, 0, 1)) }}
            </div>
            <div class="text-left flex-1">
              <div class="text-white text-sm font-bold">{{ $s->name }}</div>
              <div class="text-gray-400 text-xs">
                {{ $s->isFree() ? 'Rảnh · ' . $s->phone : 'Đang giao ' . $s->active_order_count . ' đơn' }}
              </div>
            </div>
            @if($s->isFree())<span class="text-green-400 text-xs font-bold">Chọn</span>@endif
          </button>
        </form>
        @endforeach
      </div>
      <button onclick="closeAssign()" class="w-full text-gray-400 text-sm hover:text-white py-2">Huỷ</button>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function toggleOrder(id) { document.getElementById('order-' + id).classList.toggle('hidden'); }
function openAssign(orderId) {
  document.querySelectorAll('.assign-order-id').forEach(el => el.value = orderId);
  const m = document.getElementById('assign-modal');
  m.classList.remove('hidden'); m.classList.add('flex');
}
function closeAssign(e) {
  if (!e || e.target === document.getElementById('assign-modal')) {
    const m = document.getElementById('assign-modal');
    m.classList.add('hidden'); m.classList.remove('flex');
  }
}
function toggleMap() { document.getElementById('mobile-map').classList.toggle('hidden'); }
</script>
@endpush
