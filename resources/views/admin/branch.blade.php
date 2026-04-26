@extends('layouts.admin')
@section('title', 'Chi nhánh')
@section('page_title', 'Chi nhánh')

@section('content')
<div class="h-full overflow-y-auto bg-[#0F0F0F] p-4 lg:p-6">

  {{-- Branch selector --}}
  <div class="flex gap-2 mb-6 flex-wrap">
    @foreach($branches ?? [] as $branch)
    <a href="{{ route('admin.branch', ['branch' => $branch['id']]) }}"
      class="flex items-center gap-2 px-3 lg:px-4 py-2 rounded-xl border-2 text-xs lg:text-sm font-bold transition-all
             {{ ($selectedBranch['id']??'') === $branch['id'] ? 'bg-[#FF6B35] text-white border-[#FF6B35]' : 'bg-[#1A1A1A] text-gray-400 border-[#333] hover:border-[#FF6B35]/50' }}
             {{ $branch['status']==='closed' ? 'opacity-50 pointer-events-none' : '' }}">
      <div class="w-2 h-2 rounded-full {{ $branch['status']==='open'?'bg-green-500':'bg-red-500' }}"></div>
      <span class="hidden sm:inline">{{ $branch['name'] }}</span>
      <span class="sm:hidden">{{ str_replace('Chi nhánh ','',$branch['name']) }}</span>
      @if($branch['status']==='closed')<span class="text-[10px]">(Đóng)</span>@endif
    </a>
    @endforeach
  </div>

  {{-- KPI cards --}}
  <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 lg:gap-4 mb-6">
    @foreach([
      ['label'=>'Doanh thu hôm nay','value'=>number_format($selectedBranch['revenue']??0).'đ','change'=>'+12%','icon'=>'💰','color'=>'text-green-400'],
      ['label'=>'Tổng đơn',         'value'=>($selectedBranch['orders']??0).' đơn',             'change'=>'+8%', 'icon'=>'🛍', 'color'=>'text-blue-400'],
      ['label'=>'Đánh giá TB',      'value'=>($selectedBranch['rating']??0).' ⭐',               'change'=>'+0.1','icon'=>'⭐', 'color'=>'text-yellow-400'],
      ['label'=>'TB đơn/giờ',       'value'=>round(($selectedBranch['orders']??0)/14).' đơn',    'change'=>'bình thường','icon'=>'📈','color'=>'text-orange-400'],
    ] as $kpi)
    <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-3 lg:p-4">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xl lg:text-2xl">{{ $kpi['icon'] }}</span>
        <span class="text-xs font-bold {{ $kpi['color'] }}">{{ $kpi['change'] }}</span>
      </div>
      <div class="text-white font-black text-lg lg:text-xl">{{ $kpi['value'] }}</div>
      <div class="text-gray-500 text-xs mt-0.5">{{ $kpi['label'] }}</div>
    </div>
    @endforeach
  </div>

  {{-- Main grid --}}
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">

    {{-- Revenue chart --}}
    <div class="xl:col-span-2 bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
      <h3 class="text-white font-black mb-4 text-sm lg:text-base">Doanh thu theo giờ - {{ $selectedBranch['name']??'' }}</h3>
      <div class="h-40 lg:h-52 flex items-end gap-1 px-2">
        @foreach($hourlyData ?? [] as $d)
        @php $h = min(100, ($d['total'] / 400) * 100); @endphp
        <div class="flex-1 flex flex-col items-center gap-1">
          <div class="w-full bg-[#FF6B35] rounded-t-sm hover:bg-[#FFD23F] transition-colors cursor-pointer" style="height: {{ $h }}%" title="{{ $d['hour'] }}: {{ $d['total'] }} đơn"></div>
          <span class="text-[7px] lg:text-[9px] text-gray-500 rotate-45 origin-left">{{ substr($d['hour'],0,5) }}</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Right panel --}}
    <div class="space-y-4">
      {{-- Inventory alerts --}}
      <div class="bg-[#1A1A1A] border-2 border-red-500/30 rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-3">
          <span class="text-red-400">⚠️</span>
          <span class="text-white font-black text-sm">Cảnh báo tồn kho</span>
          <span class="bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">{{ count($lowStock??[]) }}</span>
        </div>
        <div class="space-y-2">
          @forelse($lowStock ?? [] as $item)
          <div class="flex items-center justify-between text-xs rounded-xl px-3 py-2 {{ $item['status']==='critical' ? 'bg-red-900/30 border border-red-700/30' : 'bg-yellow-900/20 border border-yellow-700/20' }}">
            <span class="text-white font-bold">{{ $item['name'] }}</span>
            <div class="text-right">
              <span class="font-black {{ $item['status']==='critical'?'text-red-400':'text-yellow-400' }}">{{ $item['current'] }}/{{ $item['safety'] }} {{ $item['unit'] }}</span>
              <div class="text-[9px] {{ $item['status']==='critical'?'text-red-400':'text-yellow-400' }}">{{ $item['status']==='critical'?'HẾT GẦN!':'THẤP' }}</div>
            </div>
          </div>
          @empty
          <p class="text-gray-500 text-xs text-center py-2">Tồn kho ổn định ✓</p>
          @endforelse
        </div>
      </div>

      {{-- Refund resolution --}}
      <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-3">
          <span class="text-[#FF6B35]">📦</span>
          <span class="text-white font-black text-sm">Yêu cầu hoàn tiền</span>
          <span class="bg-[#FF6B35] text-white text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">{{ collect($refunds??[])->where('status','pending')->count() }}</span>
        </div>
        <div class="space-y-3">
          @forelse($refunds ?? [] as $refund)
          <div class="rounded-xl p-3 border {{ $refund['status']==='pending' ? 'bg-orange-900/20 border-orange-700/30' : ($refund['status']==='approved' ? 'bg-green-900/20 border-green-700/30' : 'bg-red-900/20 border-red-700/30') }}">
            <div class="flex items-center justify-between mb-1">
              <span class="text-white text-xs font-black">{{ $refund['order'] }}</span>
              <span class="text-[10px] font-black {{ $refund['status']==='pending'?'text-orange-400':($refund['status']==='approved'?'text-green-400':'text-red-400') }}">
                {{ $refund['status']==='pending'?'Chờ duyệt':($refund['status']==='approved'?'Đã duyệt':'Từ chối') }}
              </span>
            </div>
            <div class="text-gray-400 text-[10px] mb-2">{{ $refund['customer'] }} · {{ $refund['reason'] }}</div>
            <div class="flex items-center justify-between">
              <span class="text-[#FF6B35] font-black text-sm">{{ number_format($refund['amount']) }}đ</span>
              @if($refund['status']==='pending')
              <div class="flex gap-1">
                <form action="{{ route('admin.branch.refund', $refund['id']) }}" method="POST" class="inline">
                  @csrf @method('PATCH')
                  <input type="hidden" name="action" value="approve" />
                  <button type="submit" class="text-[10px] font-black bg-green-600 text-white px-2 py-1 rounded-lg hover:bg-green-500">✓ OK</button>
                </form>
                <form action="{{ route('admin.branch.refund', $refund['id']) }}" method="POST" class="inline">
                  @csrf @method('PATCH')
                  <input type="hidden" name="action" value="reject" />
                  <button type="submit" class="text-[10px] font-black bg-red-600 text-white px-2 py-1 rounded-lg hover:bg-red-500">✗ TK</button>
                </form>
              </div>
              @endif
            </div>
          </div>
          @empty
          <p class="text-gray-500 text-xs text-center py-2">Không có yêu cầu nào ✓</p>
          @endforelse
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
