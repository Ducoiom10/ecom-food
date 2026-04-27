@extends('layouts.client')
@section('title', 'Lịch sử đơn hàng')
@section('page_heading', 'Lịch sử đơn hàng')

@section('content')
<div class="p-4 lg:p-8 max-w-3xl mx-auto">

  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('client.profile') }}" class="w-9 h-9 border-2 border-[#1C1C1C] rounded-xl bg-white flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all font-bold">←</a>
    <h1 class="font-black text-[#1C1C1C] text-xl">Lịch sử đơn hàng</h1>
  </div>

  @forelse($orders ?? [] as $order)
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-3">
    <div class="flex items-center justify-between mb-3">
      <div>
        <span class="font-black text-[#1C1C1C] text-sm">{{ $order->order_number }}</span>
        <div class="text-gray-400 text-xs mt-0.5">🕐 {{ $order->created_at->format('d/m/Y H:i') }}</div>
      </div>
      @php
        $statusLabel = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','preparing'=>'Đang nấu','ready'=>'Sẵn sàng','delivering'=>'Đang giao','completed'=>'Hoàn thành','cancelled'=>'Đã huỷ'][$order->status] ?? $order->status;
        $statusColor = $order->status === 'completed' ? 'bg-green-100 text-green-600 border-green-200' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-600 border-red-200' : 'bg-orange-100 text-orange-600 border-orange-200');
      @endphp
      <span class="text-xs font-black px-2 py-0.5 rounded-full border {{ $statusColor }}">{{ $statusLabel }}</span>
    </div>

    <div class="space-y-1 mb-3">
      @foreach($order->items->take(3) as $item)
      <div class="text-xs text-gray-500">{{ $item->product->name ?? '—' }} x{{ $item->quantity }}</div>
      @endforeach
      @if($order->items->count() > 3)
      <div class="text-xs text-gray-400">+{{ $order->items->count() - 3 }} món khác</div>
      @endif
    </div>

    <div class="flex items-center justify-between">
      <span class="font-black text-[#FF6B35]">{{ number_format($order->grand_total) }}đ</span>
      <div class="flex gap-2">
        <a href="{{ route('client.order.show', $order->id) }}" class="text-xs font-bold text-gray-500 border border-gray-200 px-3 py-1.5 rounded-lg hover:border-gray-400 transition-colors">Chi tiết</a>
        <button class="flex items-center gap-1.5 bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all">
          🔄 Đặt lại
        </button>
      </div>
    </div>
  </div>
  @empty
  <div class="text-center py-16 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C]">
    <div class="text-6xl mb-4">🧾</div>
    <p class="font-black text-[#1C1C1C] text-xl">Chưa có đơn hàng nào</p>
    <a href="{{ route('client.menu') }}" class="mt-4 inline-block bg-[#FF6B35] text-white font-black px-5 py-2.5 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C]">Đặt món ngay →</a>
  </div>
  @endforelse

</div>
@endsection
