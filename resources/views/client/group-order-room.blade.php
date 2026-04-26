@extends('layouts.client')
@section('title', 'Phòng #' . ($room['code'] ?? ''))

@section('content')
<div class="min-h-screen bg-[#FAFAF8] flex flex-col max-w-[430px] mx-auto">

  {{-- Header --}}
  <div class="sticky top-0 z-30 bg-white border-b-2 border-[#1C1C1C] px-4 py-3">
    <div class="flex items-center justify-between mb-2">
      <div>
        <h1 class="font-black text-[#1C1C1C] flex items-center gap-2">
          👥 Phòng #{{ $room['code'] ?? '' }}
          @if($room['isLocked'] ?? false)<span class="text-red-500 text-sm">🔒</span>@endif
        </h1>
        <div class="flex items-center gap-1.5 mt-0.5">
          <span class="text-[10px] text-green-600 font-bold">🟢 Đồng bộ thời gian thực</span>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <div class="flex -space-x-2">
          @foreach(($room['participants'] ?? []) as $p)
          <div class="w-7 h-7 rounded-full border-2 border-white bg-[#FFD23F] flex items-center justify-center text-xs" title="{{ $p['name'] }}">
            {{ $p['emoji'] }}
          </div>
          @endforeach
        </div>
        <span class="text-xs text-gray-500 font-bold">{{ count($room['participants'] ?? []) }} người</span>
      </div>
    </div>

    {{-- My order badge --}}
    <div class="flex items-center gap-2">
      <div class="flex-1 bg-[#1C1C1C] text-[#FFD23F] text-xs font-bold px-3 py-1.5 rounded-xl flex items-center gap-1.5">
        🛍 Của tôi: <span class="text-white">{{ $myItemCount ?? 0 }} món</span>
        <span class="ml-auto">{{ number_format($myTotal ?? 0) }}đ</span>
      </div>
      @if($isHost ?? false)
      <form action="{{ route('client.group-order.lock', $room['code']) }}" method="POST">
        @csrf
        <button type="submit" class="bg-[#FF6B35] text-white text-xs font-black px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] flex items-center gap-1">
          🔒 Chốt đơn
        </button>
      </form>
      @endif
    </div>
  </div>

  {{-- Tabs --}}
  <div class="flex border-b-2 border-[#1C1C1C] bg-white">
    @foreach([['id'=>'menu','label'=>'🍽️ Chọn món'],['id'=>'orders','label'=>'👥 Đơn nhóm'],['id'=>'activity','label'=>'⚡ Hoạt động']] as $tab)
    <button onclick="switchTab('{{ $tab['id'] }}')" id="tab-{{ $tab['id'] }}"
      class="flex-1 py-2.5 text-xs font-black uppercase tracking-wide border-r last:border-r-0 border-[#1C1C1C] transition-all {{ $tab['id'] === 'menu' ? 'bg-[#FF6B35] text-white' : 'text-gray-500 hover:bg-gray-50' }}">
      {{ $tab['label'] }}
    </button>
    @endforeach
  </div>

  <div class="flex-1 overflow-y-auto">

    {{-- MENU TAB --}}
    <div id="panel-menu" class="p-4">
      @if($room['isLocked'] ?? false)
      <div class="bg-red-50 border-2 border-red-300 rounded-xl p-3 mb-4 flex items-center gap-2">
        <span>🔒</span><span class="text-sm font-bold text-red-600">Đơn đã bị khoá. Không thể thêm món.</span>
      </div>
      @endif

      {{-- Category filter --}}
      <div class="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
        @foreach([['id'=>'all','label'=>'Tất cả','emoji'=>'🍽️'],['id'=>'noodles','label'=>'Mì & Phở','emoji'=>'🍜'],['id'=>'rice','label'=>'Cơm','emoji'=>'🍚'],['id'=>'snacks','label'=>'Ăn vặt','emoji'=>'🍗'],['id'=>'drinks','label'=>'Đồ uống','emoji'=>'🧋']] as $cat)
        <button onclick="filterCat('{{ $cat['id'] }}')" id="cat-{{ $cat['id'] }}"
          class="flex-shrink-0 text-xs font-bold px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] transition-all {{ $cat['id'] === 'all' ? 'bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]' : 'bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]' }}">
          {{ $cat['emoji'] }} {{ $cat['label'] }}
        </button>
        @endforeach
      </div>

      <div class="space-y-3">
        @foreach($menuItems ?? [] as $item)
        <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden flex menu-item" data-category="{{ $item['category'] }}">
          <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-24 h-24 object-cover flex-shrink-0" />
          <div class="flex-1 p-3 flex flex-col justify-between">
            <div>
              <div class="font-black text-[#1C1C1C] text-sm leading-tight">{{ $item['name'] }}</div>
              <div class="text-[#FF6B35] font-black text-sm mt-0.5">{{ number_format($item['price']) }}đ</div>
            </div>
            <div class="flex items-center gap-2 mt-2">
              <form action="{{ route('client.group-order.item', $room['code']) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="menu_item_id" value="{{ $item['id'] }}" />
                <input type="hidden" name="action" value="remove" />
                <button type="submit" class="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C]">−</button>
              </form>
              <span class="font-black text-[#1C1C1C] min-w-[16px] text-center">{{ $myItems[$item['id']] ?? 0 }}</span>
              <form action="{{ route('client.group-order.item', $room['code']) }}" method="POST">
                @csrf
                <input type="hidden" name="menu_item_id" value="{{ $item['id'] }}" />
                <input type="hidden" name="action" value="add" />
                <button type="submit" class="flex items-center gap-1.5 bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">
                  + Thêm
                </button>
              </form>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- ORDERS TAB --}}
    <div id="panel-orders" class="p-4 space-y-3 hidden">
      @foreach($room['participants'] ?? [] as $p)
      @php $pOrder = collect($room['orders'] ?? [])->firstWhere('participantId', $p['id']); @endphp
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-8 h-8 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-full flex items-center justify-center text-sm">{{ $p['emoji'] }}</div>
          <div class="flex-1">
            <div class="font-black text-[#1C1C1C] text-sm">{{ $p['name'] }}
              @if($p['isHost'] ?? false)<span class="bg-[#FFD23F] text-[#1C1C1C] text-[9px] font-black px-1.5 py-0.5 rounded-full border border-[#1C1C1C] ml-1">Host</span>@endif
            </div>
          </div>
          <span class="font-black text-[#FF6B35]">{{ number_format($pOrder ? collect($pOrder['items'])->sum(fn($i) => ($menuPrices[$i['menuItemId']] ?? 0) * $i['quantity']) : 0) }}đ</span>
        </div>
        @if($pOrder && count($pOrder['items']) > 0)
        <div class="space-y-1.5">
          @foreach($pOrder['items'] as $it)
          <div class="flex items-center justify-between text-xs">
            <span class="text-gray-600">{{ $menuNames[$it['menuItemId']] ?? $it['menuItemId'] }}</span>
            <span class="font-bold text-[#1C1C1C]">x{{ $it['quantity'] }}</span>
          </div>
          @endforeach
        </div>
        @else
        <p class="text-xs text-gray-400 italic">Chưa chọn món nào...</p>
        @endif
      </div>
      @endforeach

      {{-- Grand total --}}
      <div class="bg-[#1C1C1C] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#FF6B35] p-4">
        <div class="flex items-center justify-between">
          <span class="text-white font-black">Tổng đơn nhóm</span>
          <span class="text-[#FFD23F] font-black text-lg">{{ number_format($grandTotal ?? 0) }}đ</span>
        </div>
        @if($isHost ?? false)
        <form action="{{ route('client.group-order.lock', $room['code']) }}" method="POST" class="mt-3">
          @csrf
          <button type="submit" class="w-full bg-[#FF6B35] text-white font-black py-3 rounded-xl border-2 border-[#FF6B35] flex items-center justify-center gap-2">
            🔒 Chốt đơn & Chia bill →
          </button>
        </form>
        @endif
      </div>
    </div>

    {{-- ACTIVITY TAB --}}
    <div id="panel-activity" class="p-4 hidden">
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden">
        <div class="bg-[#1C1C1C] px-4 py-2.5 flex items-center gap-2">
          <span class="text-[#FFD23F]">⚡</span>
          <span class="text-white text-xs font-black uppercase tracking-wide">Live Activity Feed</span>
          <div class="ml-auto flex items-center gap-1">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-green-400 text-[10px]">LIVE</span>
          </div>
        </div>
        <div class="h-96 overflow-y-auto p-3 space-y-2" id="activity-feed">
          @forelse($room['activities'] ?? [] as $act)
          <div class="flex items-start gap-2">
            <span class="text-lg flex-shrink-0">{{ $act['emoji'] }}</span>
            <div class="flex-1 bg-gray-50 rounded-xl px-3 py-2">
              <span class="font-bold text-[#1C1C1C] text-xs">{{ $act['participantName'] }}</span>
              <span class="text-gray-500 text-xs"> {{ $act['action'] }} </span>
              @if($act['itemName'])<span class="font-bold text-[#FF6B35] text-xs">"{{ $act['itemName'] }}"</span>@endif
            </div>
            <span class="text-[10px] text-gray-400 flex-shrink-0 mt-1">{{ $act['time'] }}</span>
          </div>
          @empty
          <p class="text-center text-gray-400 text-xs py-8">Chưa có hoạt động nào...</p>
          @endforelse
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
  ['menu','orders','activity'].forEach(t => {
    document.getElementById('panel-' + t).classList.toggle('hidden', t !== tab);
    const btn = document.getElementById('tab-' + t);
    if (t === tab) btn.className = btn.className.replace('text-gray-500 hover:bg-gray-50', 'bg-[#FF6B35] text-white');
    else btn.className = btn.className.replace('bg-[#FF6B35] text-white', 'text-gray-500 hover:bg-gray-50');
  });
}
function filterCat(cat) {
  document.querySelectorAll('.menu-item').forEach(el => {
    el.style.display = (cat === 'all' || el.dataset.category === cat) ? '' : 'none';
  });
  document.querySelectorAll('[id^="cat-"]').forEach(btn => {
    const id = btn.id.replace('cat-', '');
    btn.className = btn.className.replace(id === cat ? 'bg-white text-[#1C1C1C]' : 'bg-[#FF6B35] text-white', id === cat ? 'bg-[#FF6B35] text-white' : 'bg-white text-[#1C1C1C]');
  });
}

// Auto-refresh activity every 3s
setInterval(() => {
  fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.text()).then(() => { /* update via AJAX if needed */ });
}, 3000);
</script>
@endpush
