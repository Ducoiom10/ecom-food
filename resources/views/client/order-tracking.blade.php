@extends('layouts.client')
@section('title', 'Theo dõi đơn hàng #' . $order->order_number)
@section('page_heading', 'Theo dõi đơn hàng')

@section('content')
@php
$steps = [
    ['key' => 'pending',   'label' => 'Đã đặt',      'icon' => '📋', 'time' => $order->created_at],
    ['key' => 'confirmed', 'label' => 'Xác nhận',    'icon' => '✅', 'time' => $order->confirmed_at],
    ['key' => 'preparing', 'label' => 'Đang làm',    'icon' => '👨‍🍳', 'time' => $order->preparing_at],
    ['key' => 'ready',     'label' => 'Sẵn sàng',    'icon' => '🎉', 'time' => $order->ready_at],
    ['key' => 'completed', 'label' => $order->delivery_mode === 'delivery' ? 'Đã giao' : 'Đã lấy', 'icon' => $order->delivery_mode === 'delivery' ? '🛵' : '🏪', 'time' => $order->completed_at],
];
$statusOrder = ['pending','confirmed','preparing','ready','completed','cancelled'];
$currentIdx  = array_search($order->status, $statusOrder);
@endphp

<div class="p-4 lg:p-8 max-w-3xl mx-auto space-y-4">

  {{-- Header card --}}
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="text-xs text-gray-400 font-medium">Mã đơn hàng</p>
        <p class="font-black text-[#1C1C1C] text-lg tracking-wide">{{ $order->order_number }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $order->branch->name ?? '—' }} · {{ $order->created_at->format('H:i d/m/Y') }}</p>
      </div>
      @php
        $statusConfig = [
          'pending'   => ['bg-yellow-100 text-yellow-700 border-yellow-300', '⏳ Chờ xác nhận'],
          'confirmed' => ['bg-blue-100 text-blue-700 border-blue-300',       '✅ Đã xác nhận'],
          'preparing' => ['bg-orange-100 text-orange-700 border-orange-300', '👨‍🍳 Đang chuẩn bị'],
          'ready'     => ['bg-green-100 text-green-700 border-green-300',    '🎉 Sẵn sàng'],
          'completed' => ['bg-gray-100 text-gray-700 border-gray-300',       '✔️ Hoàn thành'],
          'cancelled' => ['bg-red-100 text-red-700 border-red-300',          '❌ Đã huỷ'],
        ];
        [$cls, $lbl] = $statusConfig[$order->status] ?? ['bg-gray-100 text-gray-600 border-gray-200', $order->status];
      @endphp
      <span class="px-3 py-1.5 rounded-xl border-2 text-xs font-black {{ $cls }}">{{ $lbl }}</span>
    </div>
  </div>

  {{-- Timeline --}}
  @if($order->status !== 'cancelled')
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5">
    <p class="font-black text-[#1C1C1C] text-sm mb-5">Trạng thái đơn hàng</p>
    <div class="relative">
      {{-- Vertical line --}}
      <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
      <div class="space-y-6">
        @foreach($steps as $i => $step)
        @php
          $done    = $currentIdx !== false && $i <= $currentIdx;
          $current = $currentIdx !== false && $i === $currentIdx;
        @endphp
        <div class="flex items-start gap-4 relative">
          <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center flex-shrink-0 z-10 text-base transition-all
            {{ $done ? 'bg-[#FF6B35] border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]' : 'bg-white border-gray-300' }}">
            {{ $step['icon'] }}
          </div>
          <div class="flex-1 pt-1.5">
            <p class="font-black text-sm {{ $done ? 'text-[#1C1C1C]' : 'text-gray-400' }}">{{ $step['label'] }}</p>
            @if($step['time'])
            <p class="text-xs text-gray-400 mt-0.5">{{ $step['time']->format('H:i · d/m/Y') }}</p>
            @elseif($current)
            <p class="text-xs text-[#FF6B35] font-bold mt-0.5 animate-pulse">Đang xử lý...</p>
            @endif
          </div>
          @if($current)
          <span class="text-xs bg-[#FFD23F] border border-[#1C1C1C] rounded-lg px-2 py-0.5 font-black text-[#1C1C1C]">Hiện tại</span>
          @endif
        </div>
        @endforeach
      </div>
    </div>

    {{-- ETA --}}
    @if(in_array($order->status, ['confirmed','preparing']))
    <div class="mt-5 bg-orange-50 border-2 border-[#FF6B35] rounded-xl p-3 flex items-center gap-3">
      <span class="text-2xl">⏱️</span>
      <div>
        <p class="font-black text-[#1C1C1C] text-sm">Dự kiến {{ $order->delivery_mode === 'delivery' ? 'giao hàng' : 'sẵn sàng' }}</p>
        <p class="text-xs text-gray-500">~{{ $order->estimated_eta ?? '20-25' }} phút kể từ khi xác nhận</p>
      </div>
    </div>
    @endif
  </div>
  @endif

  {{-- Shipper info --}}
  @if($order->shipper && $order->status === 'ready')
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 flex items-center gap-4">
    <div class="w-12 h-12 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center text-white text-xl">🛵</div>
    <div class="flex-1">
      <p class="font-black text-[#1C1C1C] text-sm">{{ $order->shipper->name }}</p>
      <p class="text-xs text-gray-500">Shipper · {{ $order->shipper->phone }}</p>
    </div>
    <a href="tel:{{ $order->shipper->phone }}" class="bg-green-500 text-white text-xs font-black px-3 py-2 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">📞 Gọi</a>
  </div>
  @endif

  {{-- Order items --}}
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5">
    <p class="font-black text-[#1C1C1C] text-sm mb-4">Chi tiết đơn ({{ $order->items->count() }} món)</p>
    <div class="space-y-3">
      @foreach($order->items as $item)
      <div class="flex items-center gap-3">
        @if($item->product?->image)
        <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded-xl border-2 border-gray-100 flex-shrink-0" />
        @else
        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-xl flex-shrink-0">🍽️</div>
        @endif
        <div class="flex-1">
          <p class="font-bold text-[#1C1C1C] text-sm">{{ $item->product->name ?? 'Sản phẩm' }}</p>
          @if($item->note)<p class="text-xs text-orange-500">{{ $item->note }}</p>@endif
        </div>
        <div class="text-right">
          <p class="font-black text-[#1C1C1C] text-sm">x{{ $item->quantity }}</p>
          <p class="text-xs text-gray-500">{{ number_format($item->price * $item->quantity) }}đ</p>
        </div>
      </div>
      @endforeach
    </div>

    <div class="border-t-2 border-dashed border-gray-200 mt-4 pt-4 space-y-1.5 text-sm">
      <div class="flex justify-between text-gray-500">
        <span>Tạm tính</span><span>{{ number_format($order->subtotal) }}đ</span>
      </div>
      @if($order->shipping_fee > 0)
      <div class="flex justify-between text-gray-500">
        <span>Phí vận chuyển</span><span>{{ number_format($order->shipping_fee) }}đ</span>
      </div>
      @endif
      @if($order->discount_amount > 0)
      <div class="flex justify-between text-green-600">
        <span>Giảm giá</span><span>-{{ number_format($order->discount_amount) }}đ</span>
      </div>
      @endif
      <div class="flex justify-between font-black text-[#1C1C1C] text-base pt-1 border-t border-gray-100">
        <span>Tổng cộng</span><span class="text-[#FF6B35]">{{ number_format($order->grand_total) }}đ</span>
      </div>
      <div class="flex justify-between text-xs text-gray-400 pt-1">
        <span>Thanh toán</span>
        <span>{{ ['momo'=>'💜 MoMo','cod'=>'💵 Tiền mặt','zalopay'=>'🔵 ZaloPay','bank'=>'🏦 Chuyển khoản'][$order->payment_method] ?? $order->payment_method }}</span>
      </div>
    </div>
  </div>

  {{-- Actions --}}
  <div class="flex gap-3">
    <a href="{{ route('client.profile') }}" class="flex-1 text-center py-3 rounded-xl border-2 border-[#1C1C1C] bg-white font-black text-sm shadow-[3px_3px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
      ← Lịch sử đơn
    </a>
    @if($order->status === 'completed')
    <a href="{{ route('client.menu') }}" class="flex-1 text-center py-3 rounded-xl border-2 border-[#1C1C1C] bg-[#FF6B35] text-white font-black text-sm shadow-[3px_3px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
      🔄 Đặt lại
    </a>
    @endif
  </div>

</div>
@endsection
