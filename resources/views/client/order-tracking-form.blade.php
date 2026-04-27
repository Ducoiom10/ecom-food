@extends('layouts.client')
@section('title', 'Tra cứu đơn hàng')
@section('page_heading', 'Tra cứu đơn hàng')

@section('content')
    <div class="p-4 lg:p-8 max-w-md mx-auto">
        <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-6 space-y-5">
            <div class="text-center">
                <div class="text-4xl mb-2">📦</div>
                <h1 class="font-black text-[#1C1C1C] text-lg">Theo dõi đơn hàng</h1>
                <p class="text-gray-500 text-sm mt-1">Nhập mã đơn và số điện thoại để tra cứu</p>
            </div>

            @if ($errors->has('track'))
                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-3 text-red-700 text-sm font-bold">
                    {{ $errors->first('track') }}
                </div>
            @endif

            <form action="{{ route('client.track-order.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">Mã đơn
                        hàng</label>
                    <input type="text" name="order_number" value="{{ old('order_number') }}"
                        placeholder="BAE-Q1-20260101-001" required
                        class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all" />
                </div>
                <div>
                    <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">Số điện
                        thoại</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="0901 234 567" required
                        class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all" />
                </div>
                <button type="submit"
                    class="w-full bg-[#FF6B35] text-white font-black py-3 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
                    🔍 Tra cứu
                </button>
            </form>

            <div class="text-center text-xs text-gray-400">
                Hoặc <a href="{{ route('login') }}" class="text-[#FF6B35] font-bold underline">đăng nhập</a> để xem tất cả
                đơn hàng
            </div>
        </div>
    </div>
@endsection
