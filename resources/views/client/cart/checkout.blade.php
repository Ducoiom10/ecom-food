@extends('layouts.client')
@section('title', 'Thanh toán')

@section('content')
<div class="p-4 lg:p-8 max-w-4xl mx-auto">
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-8 mb-8">
    <h1 class="font-black text-3xl text-[#1C1C1C] mb-4">✅ XÁC NHẬN ĐƠN HÀNG</h1>
    <div class="grid md:grid-cols-2 gap-8">
      {{-- Order summary --}}
      <div>
        <div class="space-y-3 mb-6">
          <div class="flex justify-between text-xl">
            <span>{{ count($cart) }} món</span>
            <span class="font-black text-[#FF6B35]">{{ number_format($subtotal) }}đ</span>
          </div>
          <div class="flex justify-between">
            <span>Phí ship</span>
            <span>{{ $shippingFee ? number_format($shippingFee) . 'đ' : 'Miễn phí' }}</span>
          </div>
          @if($voucher)
          <div class="flex justify-between text-green-600">
            <span>{{ $voucher->code }}</span>
            <span class="font-black">-{{ number_format($voucher->discount) }}đ</span>
          </div>
          @endif
          <div class="border-t-2 pt-4">
            <div class="flex justify-between text-2xl font-black">
              <span>TỔNG THANH TOÁN</span>
              <span class="text-[#FF6B35]">{{ number_format($subtotal + $shippingFee - ($voucher->discount ?? 0)) }}đ</span>
            </div>
          </div>
        </div>
        <div class="space-y-3">
          <div class="font-bold text-lg">Chi nhánh: {{ $branch->name }}</div>
          <div>⏱️ Ước tính: ~25 phút</div>
          <div>📱 Theo dõi: SMS/{{ $user->phone ?? 'email' }}</div>
        </div>
      </div>
      {{-- Action buttons --}}
      <div class="flex flex-col gap-4">
        <a href="{{ route('client.cart') }}" class="bg-gray-200 text-[#1C1C1C] font-black py-4 px-6 rounded-2xl text-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all">← Sửa đơn</a>
        <form method="POST" action="{{ route('checkout.post') }}">
          @csrf
          <input type="hidden" name="confirm" value="1">
          <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 text-white font-black py-5 px-6 rounded-2xl shadow-[4px_4px_0px_#1C1C1C] hover:shadow-[2px_2px_0px_#1C1C1C] transition-all text-lg">
            🎉 HOÀN TẤT ĐƠN HÀNG
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
