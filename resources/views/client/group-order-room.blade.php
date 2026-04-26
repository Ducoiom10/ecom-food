@extends('layouts.client')
@section('title', 'Phòng #' . $room->room_code)

@section('content')
<div class="min-h-screen bg-[#FAFAF8] flex flex-col max-w-[430px] mx-auto">

  {{-- Header --}}
  <div class="sticky top-0 z-30 bg-white border-b-2 border-[#1C1C1C] px-4 py-3">
    <div class="flex items-center justify-between mb-2">
      <div>
        <h1 class="font-black text-[#1C1C1C] flex items-center gap-2">
          👥 Phòng #{{ $room->room_code }}
          @if($room->is_locked)<span class="text-red-500 text-sm">🔒</span>@endif
        </h1>
        <div class="flex items-center gap-1.5 mt-0.5">
          <span class="text-[10px] text-green-600 font-bold">🟢 Đồng bộ thời gian thực</span>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <div class="flex -space-x-2">
          @foreach($room->participants as $p)
          <div class="w-7 h-7 rounded-full border-2 border-white bg-[#FFD23F] flex items-center justify-center text-xs" title="{{ $p->display_name }}">
            {{ $p->emoji ?? '👤' }}
          </div>
          @endforeach
        </div>
        <span class="text-xs text-gray-500 font-bold">{{ $room->participants->count() }} người</span>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <div class="flex-1 bg-[#1C1C1C] text-[#FFD23F] text-xs font-bold px-3 py-1.5 rounded-xl flex items-center gap-1.5">
        🛍 Của tôi: <span class="text-white">{{ $myItemCount }} món</span>
        <span class="ml-auto">{{ number_format($myTotal) }}đ</span>
      </div>
      @if($isHost && !$room->is_locked)
      <form action="{{ route('client.group-order.lock', $room->room_code) }}" method="POST">
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
    @foreach([['id'=>'menu','label'=>'🍽️ Chọn món'],['id'=>'orders','label'=>'👥 Đơn nhóm'],['id'=>'share','label'=>'🔗 Mời bạn']] as $tab)
    <button onclick="switchTab('{{ $tab['id'] }}')" id="tab-{{ $tab['id'] }}"
      class="flex-1 py-2.5 text-xs font-black uppercase tracking-wide border-r last:border-r-0 border-[#1C1C1C] transition-all {{ $tab['id'] === 'menu' ? 'bg-[#FF6B35] text-white' : 'text-gray-500 hover:bg-gray-50' }}">
      {{ $tab['label'] }}
    </button>
    @endforeach
  </div>

  <div class="flex-1 overflow-y-auto">

    {{-- MENU TAB --}}
    <div id="panel-menu" class="p-4">
      @if($room->is_locked)
      <div class="bg-red-50 border-2 border-red-300 rounded-xl p-3 mb-4 flex items-center gap-2">
        <span>🔒</span><span class="text-sm font-bold text-red-600">Đơn đã bị khoá. Không thể thêm món.</span>
      </div>
      @endif

      {{-- Category filter --}}
      <div class="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
        @foreach([['slug'=>'all','name'=>'Tất cả','icon'=>'🍽️'],['slug'=>'noodles','name'=>'Mì & Phở','icon'=>'🍜'],['slug'=>'rice','name'=>'Cơm','icon'=>'🍚'],['slug'=>'snacks','name'=>'Ăn vặt','icon'=>'🍗'],['slug'=>'drinks','name'=>'Đồ uống','icon'=>'🧋']] as $cat)
        <button onclick="filterCat('{{ $cat['slug'] }}')" id="cat-{{ $cat['slug'] }}"
          class="flex-shrink-0 text-xs font-bold px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] transition-all {{ $cat['slug'] === 'all' ? 'bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]' : 'bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]' }}">
          {{ $cat['icon'] }} {{ $cat['name'] }}
        </button>
        @endforeach
      </div>

      <div class="space-y-3">
        @foreach($products as $product)
        @php $myQty = $myItems->firstWhere('product_id', $product->id)?->quantity ?? 0; @endphp
        <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden flex menu-item" data-category="{{ $product->category?->slug ?? 'all' }}">
          <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover flex-shrink-0" />
          <div class="flex-1 p-3 flex flex-col justify-between">
            <div>
              <div class="font-black text-[#1C1C1C] text-sm leading-tight">{{ $product->name }}</div>
              <div class="text-[#FF6B35] font-black text-sm mt-0.5">{{ number_format($product->base_price) }}đ</div>
            </div>
            @if(!$room->is_locked)
            <div class="flex items-center gap-2 mt-2">
              <form action="{{ route('client.group-order.item', $room->room_code) }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                <input type="hidden" name="action" value="remove" />
                <button type="submit" class="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C]">−</button>
              </form>
              <span class="font-black text-[#1C1C1C] min-w-[16px] text-center">{{ $myQty }}</span>
              <form action="{{ route('client.group-order.item', $room->room_code) }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                <input type="hidden" name="action" value="add" />
                <button type="submit" class="flex items-center gap-1.5 bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">
                  + Thêm
                </button>
              </form>
            </div>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- ORDERS TAB --}}
    <div id="panel-orders" class="p-4 space-y-3 hidden">
      @foreach($room->participants as $p)
      @php $pOrder = $p->orders->first(); @endphp
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-8 h-8 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-full flex items-center justify-center text-sm">{{ $p->emoji ?? '👤' }}</div>
          <div class="flex-1">
            <div class="font-black text-[#1C1C1C] text-sm">{{ $p->display_name }}
              @if($p->is_host)<span class="bg-[#FFD23F] text-[#1C1C1C] text-[9px] font-black px-1.5 py-0.5 rounded-full border border-[#1C1C1C] ml-1">Host</span>@endif
            </div>
          </div>
          <span class="font-black text-[#FF6B35]">{{ number_format($pOrder?->grand_total ?? 0) }}đ</span>
        </div>
        @if($pOrder && $pOrder->items->count() > 0)
        <div class="space-y-1.5">
          @foreach($pOrder->items as $it)
          <div class="flex items-center justify-between text-xs">
            <span class="text-gray-600">{{ $it->product?->name ?? 'Sản phẩm' }}</span>
            <span class="font-bold text-[#1C1C1C]">x{{ $it->quantity }}</span>
          </div>
          @endforeach
        </div>
        @else
        <p class="text-xs text-gray-400 italic">Chưa chọn món nào...</p>
        @endif
      </div>
      @endforeach

      <div class="bg-[#1C1C1C] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#FF6B35] p-4">
        <div class="flex items-center justify-between">
          <span class="text-white font-black">Tổng đơn nhóm</span>
          <span class="text-[#FFD23F] font-black text-lg">{{ number_format($grandTotal) }}đ</span>
        </div>
        @if($isHost && !$room->is_locked)
        <form action="{{ route('client.group-order.lock', $room->room_code) }}" method="POST" class="mt-3">
          @csrf
          <button type="submit" class="w-full bg-[#FF6B35] text-white font-black py-3 rounded-xl border-2 border-[#FF6B35] flex items-center justify-center gap-2">
            🔒 Chốt đơn & Chia bill →
          </button>
        </form>
        @endif
      </div>
    </div>

    {{-- SHARE TAB --}}
    <div id="panel-share" class="p-4 hidden">
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-5 text-center">
        <div class="text-5xl mb-3">🔗</div>
        <h3 class="font-black text-[#1C1C1C] text-lg mb-2">Mời bạn vào phòng</h3>
        <div class="bg-[#F5F5F0] border-2 border-[#1C1C1C] rounded-xl p-4 mb-4">
          <div class="text-3xl font-black text-[#FF6B35] tracking-widest">{{ $room->room_code }}</div>
          <div class="text-xs text-gray-500 mt-1">Mã phòng</div>
        </div>
        <button onclick="copyCode('{{ $room->room_code }}')"
          class="w-full bg-[#FFD23F] text-[#1C1C1C] font-black py-3 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C] flex items-center justify-center gap-2">
          📋 Sao chép mã
        </button>
        <p class="text-xs text-gray-400 mt-3">Bạn bè vào <strong>{{ url('/group-order/join') }}</strong> và nhập mã trên</p>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
  ['menu','orders','share'].forEach(t => {
    document.getElementById('panel-' + t).classList.toggle('hidden', t !== tab);
    const btn = document.getElementById('tab-' + t);
    if (t === tab) {
      btn.classList.remove('text-gray-500','hover:bg-gray-50');
      btn.classList.add('bg-[#FF6B35]','text-white');
    } else {
      btn.classList.remove('bg-[#FF6B35]','text-white');
      btn.classList.add('text-gray-500','hover:bg-gray-50');
    }
  });
}
function filterCat(cat) {
  document.querySelectorAll('.menu-item').forEach(el => {
    el.style.display = (cat === 'all' || el.dataset.category === cat) ? '' : 'none';
  });
  document.querySelectorAll('[id^="cat-"]').forEach(btn => {
    const id = btn.id.replace('cat-', '');
    if (id === cat) {
      btn.classList.add('bg-[#FF6B35]','text-white');
      btn.classList.remove('bg-white','text-[#1C1C1C]');
    } else {
      btn.classList.remove('bg-[#FF6B35]','text-white');
      btn.classList.add('bg-white','text-[#1C1C1C]');
    }
  });
}
function copyCode(code) {
  navigator.clipboard.writeText(code).then(() => alert('Đã sao chép mã: ' + code));
}
</script>
@endpush
