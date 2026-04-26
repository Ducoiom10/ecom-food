@extends('layouts.admin')
@section('title', 'Bếp KDS')
@section('page_title', 'Bếp KDS')

@section('content')
<div class="flex flex-col h-full bg-[#0F0F0F]">

  {{-- KDS Sub-header --}}
  <div class="bg-[#1A1A1A] border-b-2 border-[#333] px-4 lg:px-6 py-3 flex items-center justify-between flex-shrink-0">
    <div class="flex items-center gap-3">
      <div class="flex items-center gap-1.5">
        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
        <span class="text-green-400 text-xs font-bold">Online</span>
      </div>
      <button onclick="toggleOnline()" id="online-toggle"
        class="text-xs px-3 py-1.5 rounded-lg border border-gray-600 text-gray-400 hover:border-red-500 hover:text-red-400 font-bold transition-all hidden sm:block">
        Mô phỏng Offline
      </button>
    </div>
    <div class="flex items-center gap-2 lg:gap-3">
      {{-- View toggle --}}
      <div class="flex border border-[#444] rounded-xl overflow-hidden">
        <button onclick="switchView('kanban')" id="view-kanban"
          class="px-3 py-1.5 text-xs font-bold bg-[#FF6B35] text-white transition-all">👨🍳 <span class="hidden sm:inline">Kanban</span></button>
        <button onclick="switchView('inventory')" id="view-inventory"
          class="px-3 py-1.5 text-xs font-bold text-gray-400 hover:text-white transition-all">📦 <span class="hidden sm:inline">Kho</span></button>
      </div>
      <div class="text-gray-400 text-xs font-mono" id="kds-clock">{{ now()->format('H:i') }}</div>
    </div>
  </div>

  {{-- KANBAN VIEW --}}
  <div id="view-kanban-panel" class="flex-1 overflow-hidden">
    {{-- Mobile: vertical stack | Desktop: horizontal kanban --}}
    <div class="h-full flex flex-col lg:flex-row overflow-auto lg:overflow-hidden">

      @php
        $columns = [
          ['status'=>'todo',    'title'=>'Cần Làm',   'color'=>'bg-green-500',  'next'=>'cooking','action'=>'Bắt đầu nấu',   'actionColor'=>'bg-green-500 hover:bg-green-600 text-white'],
          ['status'=>'cooking', 'title'=>'Đang Nấu',  'color'=>'bg-yellow-500', 'next'=>'ready',  'action'=>'Xong! Giao bàn','actionColor'=>'bg-yellow-500 hover:bg-yellow-600 text-black'],
          ['status'=>'ready',   'title'=>'Sẵn Sàng',  'color'=>'bg-blue-500',   'next'=>'done',   'action'=>'Đã giao xong ✓','actionColor'=>'bg-blue-500 hover:bg-blue-600 text-white'],
        ];
      @endphp

      @foreach($columns as $col)
      @php $colOrders = collect($orders ?? [])->where('status', $col['status']); @endphp
      <div class="flex-1 flex flex-col border-b-2 lg:border-b-0 lg:border-r border-[#222] min-w-0 min-h-[300px] lg:min-h-0">
        {{-- Column header --}}
        <div class="px-4 py-3 border-b-2 border-[#333] flex items-center justify-between flex-shrink-0 bg-[#1A1A1A]">
          <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full {{ $col['color'] }}"></div>
            <span class="text-white font-black text-sm uppercase tracking-wide">{{ $col['title'] }}</span>
          </div>
          <span class="{{ $col['color'] }} text-white text-xs font-black w-6 h-6 rounded-full flex items-center justify-center">{{ $colOrders->count() }}</span>
        </div>

        {{-- Cards --}}
        <div class="flex-1 overflow-y-auto p-3 space-y-3">
          @forelse($colOrders as $order)
          <div class="bg-[#1A1A1A] border-2 rounded-2xl overflow-hidden transition-all
            {{ $order['priority']==='high' ? 'border-[#FF6B35] shadow-[4px_4px_0px_#FF6B35]' : ($order['elapsed']>20 ? 'border-red-500 shadow-[4px_4px_0px_#ef4444]' : 'border-[#333] shadow-[4px_4px_0px_#333]') }}">

            <div class="px-4 py-3 border-b border-[#333] flex items-center justify-between">
              <div>
                <div class="text-white font-black text-xl">{{ $order['id'] }}</div>
                <div class="text-gray-400 text-xs">{{ $order['table'] }}</div>
              </div>
              <div class="text-right">
                <div class="font-black text-xl {{ $order['elapsed']<10 ? 'text-green-400' : ($order['elapsed']<20 ? 'text-yellow-400' : 'text-red-400') }}">
                  {{ $order['elapsed'] }}m
                </div>
                @if($order['priority']==='high')
                <div class="text-[#FF6B35] text-[10px] font-black">⚡ ƯU TIÊN</div>
                @endif
                @if($order['elapsed']>20)
                <div class="text-red-400 text-[10px] font-black">⚠️ TRỄ!</div>
                @endif
              </div>
            </div>

            <div class="px-4 py-3 space-y-2">
              @foreach($order['items'] as $item)
              <div class="flex gap-3">
                <div class="w-9 h-9 bg-[#FF6B35] rounded-xl flex items-center justify-center text-white font-black text-xl flex-shrink-0">{{ $item['qty'] }}</div>
                <div class="flex-1">
                  <div class="text-white font-black text-base leading-tight">{{ $item['name'] }}</div>
                  @if(!empty($item['toppings']))
                  <div class="text-[#FFD23F] text-xs mt-0.5">+ {{ implode(', ', $item['toppings']) }}</div>
                  @endif
                  @if($item['note'])
                  <div class="text-orange-400 text-xs font-bold mt-0.5">⚠️ {{ $item['note'] }}</div>
                  @endif
                </div>
              </div>
              @endforeach
            </div>

            <div class="px-4 pb-4">
              <form action="{{ route('admin.kds.move', $order['id']) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="{{ $col['next'] }}" />
                <button type="submit" class="w-full py-3 rounded-xl font-black text-base border-2 border-[#444] transition-all {{ $col['actionColor'] }}">
                  {{ $col['action'] }}
                </button>
              </form>
            </div>
          </div>
          @empty
          <div class="text-center py-12 text-gray-600">
            <div class="text-5xl mb-2 opacity-30">👨🍳</div>
            <p class="text-sm">Trống</p>
          </div>
          @endforelse
        </div>
      </div>
      @endforeach

    </div>
  </div>

  {{-- INVENTORY VIEW --}}
  <div id="view-inventory-panel" class="flex-1 overflow-y-auto p-4 lg:p-6 hidden">
    <div class="max-w-3xl mx-auto">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-white font-black text-lg lg:text-xl flex items-center gap-2">📦 Quản lý Nguyên liệu</h2>
        <span class="text-gray-400 text-xs bg-[#1A1A1A] border border-[#333] px-3 py-1 rounded-lg hidden sm:block">Toggle để đánh dấu HẾT HÀNG</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($inventory ?? [] as $item)
        <div class="bg-[#1A1A1A] border-2 rounded-2xl p-4 transition-all
          {{ $item['status']==='critical' ? 'border-red-400' : ($item['status']==='low' ? 'border-yellow-400' : 'border-[#333]') }}">
          <div class="flex items-center gap-4">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="text-white font-black">{{ $item['name'] }}</span>
                @if($item['status']==='critical')
                <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full animate-pulse">HẾT GẦN</span>
                @elseif($item['status']==='low')
                <span class="bg-yellow-500 text-black text-[10px] font-black px-2 py-0.5 rounded-full">THẤP</span>
                @endif
              </div>
              <div class="text-xs text-gray-400 mb-2">Còn: <span class="text-white font-bold">{{ $item['current'] }} {{ $item['unit'] }}</span> / Ngưỡng: {{ $item['safety'] }}</div>
              <div class="h-2 bg-[#333] rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ $item['status']==='critical' ? 'bg-red-500' : ($item['status']==='low' ? 'bg-yellow-500' : 'bg-green-500') }}"
                  style="width: {{ min(100, ($item['current'] / max(1,$item['max'])) * 100) }}%"></div>
              </div>
            </div>
            <form action="{{ route('admin.kds.inventory', $item['id']) }}" method="POST">
              @csrf @method('PATCH')
              <button type="submit" class="w-16 h-8 rounded-full border-2 transition-all flex items-center {{ $item['isAvailable'] ? 'bg-green-500 border-green-500 justify-end' : 'bg-gray-700 border-gray-600 justify-start' }}">
                <div class="w-7 h-7 bg-white rounded-full shadow-md mx-0.5"></div>
              </button>
            </form>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function switchView(view) {
  ['kanban','inventory'].forEach(v => {
    document.getElementById('view-' + v + '-panel').classList.toggle('hidden', v !== view);
    const btn = document.getElementById('view-' + v);
    if (v === view) { btn.classList.add('bg-[#FF6B35]','text-white'); btn.classList.remove('text-gray-400'); }
    else { btn.classList.remove('bg-[#FF6B35]','text-white'); btn.classList.add('text-gray-400'); }
  });
}
setInterval(() => {
  document.getElementById('kds-clock').textContent = new Date().toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'});
}, 1000);
</script>
@endpush
