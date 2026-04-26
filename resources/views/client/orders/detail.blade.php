@extends('layouts.client')
@section('title', 'Đơn hàng ' . $order->order_number)
@section('page_heading', 'Chi tiết đơn hàng')

@section('content')
<div class="p-4 lg:p-8 max-w-2xl mx-auto">

  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('client.profile') }}" class="w-9 h-9 border-2 border-[#1C1C1C] rounded-xl bg-white flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all font-bold">←</a>
    <h1 class="font-black text-[#1C1C1C] text-xl">{{ $order->order_number }}</h1>
  </div>

  {{-- Status --}}
  @php
    $statusConfig = [
      'pending'    => ['label'=>'Chờ xác nhận', 'color'=>'bg-gray-100 text-gray-600 border-gray-200'],
      'confirmed'  => ['label'=>'Đã xác nhận',  'color'=>'bg-blue-100 text-blue-600 border-blue-200'],
      'preparing'  => ['label'=>'Đang nấu',      'color'=>'bg-yellow-100 text-yellow-700 border-yellow-200'],
      'ready'      => ['label'=>'Sẵn sàng',      'color'=>'bg-purple-100 text-purple-600 border-purple-200'],
      'delivering' => ['label'=>'Đang giao',     'color'=>'bg-orange-100 text-orange-600 border-orange-200'],
      'completed'  => ['label'=>'Hoàn thành',    'color'=>'bg-green-100 text-green-600 border-green-200'],
      'cancelled'  => ['label'=>'Đã huỷ',        'color'=>'bg-red-100 text-red-600 border-red-200'],
    ];
    $cfg = $statusConfig[$order->status] ?? $statusConfig['pending'];
  @endphp

  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5 mb-4">
    <div class="flex items-center justify-between mb-4">
      <span class="text-xs font-black px-3 py-1.5 rounded-full border-2 {{ $cfg['color'] }}">{{ $cfg['label'] }}</span>
      <span class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
    </div>

    {{-- Items --}}
    <div class="space-y-3 mb-4">
      @foreach($order->items as $item)
      <div class="flex items-center gap-3">
        <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded-xl border border-gray-200 flex-shrink-0" />
        <div class="flex-1">
          <div class="font-bold text-sm text-[#1C1C1C]">{{ $item->product->name }}</div>
          @if($item->note)<div class="text-xs text-orange-500">{{ $item->note }}</div>@endif
        </div>
        <div class="text-right">
          <div class="text-xs text-gray-400">x{{ $item->quantity }}</div>
          <div class="font-black text-[#FF6B35] text-sm">{{ number_format($item->price * $item->quantity) }}đ</div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Summary --}}
    <div class="border-t-2 border-gray-100 pt-3 space-y-1.5 text-sm">
      <div class="flex justify-between text-gray-600"><span>Tạm tính</span><span>{{ number_format($order->subtotal) }}đ</span></div>
      <div class="flex justify-between text-gray-600"><span>Phí vận chuyển</span><span>{{ number_format($order->shipping_fee) }}đ</span></div>
      @if($order->discount_amount > 0)
      <div class="flex justify-between text-green-600"><span>Giảm giá</span><span>-{{ number_format($order->discount_amount) }}đ</span></div>
      @endif
      <div class="flex justify-between font-black text-[#1C1C1C] text-base pt-1 border-t border-gray-100">
        <span>Tổng cộng</span>
        <span class="text-[#FF6B35]">{{ number_format($order->grand_total) }}đ</span>
      </div>
    </div>
  </div>

  {{-- Delivery info --}}
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-4 space-y-2">
    <div class="font-black text-[#1C1C1C] text-sm mb-2">Thông tin giao hàng</div>
    <div class="text-sm text-gray-600 flex items-start gap-2"><span>📍</span><span>{{ $order->delivery_address ?? 'Tự đến lấy' }}</span></div>
    <div class="text-sm text-gray-600 flex items-center gap-2"><span>🏪</span><span>{{ $order->branch->name ?? '—' }}</span></div>
    <div class="text-sm text-gray-600 flex items-center gap-2"><span>💳</span><span>{{ strtoupper($order->payment_method) }}</span></div>
    @if($order->shipper)
    <div class="text-sm text-gray-600 flex items-center gap-2"><span>🚚</span><span>{{ $order->shipper->name }}</span></div>
    @endif
  </div>

  <a href="{{ route('client.profile') }}" class="block text-center text-sm text-[#FF6B35] font-bold py-2 hover:underline">
    ← Quay lại lịch sử đơn
  </a>

</div>
@endsection
