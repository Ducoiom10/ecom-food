@extends('layouts.client')
@section('title', 'Chỉnh sửa hồ sơ')
@section('page_heading', 'Chỉnh sửa hồ sơ')

@section('content')
<div class="p-4 lg:p-8 max-w-2xl mx-auto">

  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('client.profile') }}" class="w-9 h-9 border-2 border-[#1C1C1C] rounded-xl bg-white flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all font-bold">←</a>
    <h1 class="font-black text-[#1C1C1C] text-xl">Chỉnh sửa hồ sơ</h1>
  </div>

  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-6 mb-4">
    <div class="flex items-center gap-4 mb-6">
      <div class="w-20 h-20 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-2xl flex items-center justify-center text-4xl shadow-[3px_3px_0px_#1C1C1C]">👤</div>
      <div>
        <button class="bg-[#FFD23F] text-[#1C1C1C] font-black text-sm px-4 py-2 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all">
          📷 Đổi ảnh
        </button>
        <p class="text-xs text-gray-400 mt-1">JPG, PNG tối đa 2MB</p>
      </div>
    </div>

    <form action="{{ route('client.profile.update') }}" method="POST" class="space-y-4">
      @csrf @method('POST')

      @if(session('success'))
      <div class="bg-green-50 border-2 border-green-300 rounded-xl px-4 py-3 text-green-700 text-sm font-bold">✅ {{ session('success') }}</div>
      @endif

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">Họ và tên</label>
          <input type="text" name="name" value="{{ $user->name }}"
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-2.5 text-sm outline-none focus:border-[#FF6B35] transition-all" />
        </div>
        <div>
          <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">Số điện thoại</label>
          <input type="tel" value="{{ $user->phone }}" disabled
            class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-400 cursor-not-allowed" />
        </div>
      </div>

      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">Email</label>
        <input type="email" name="email" value="{{ $user->email }}"
          class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-2.5 text-sm outline-none focus:border-[#FF6B35] transition-all" />
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit"
          class="flex-1 bg-[#FF6B35] text-white font-black py-3 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
          💾 Lưu thay đổi
        </button>
        <a href="{{ route('client.profile') }}"
          class="px-6 py-3 border-2 border-[#1C1C1C] rounded-xl font-bold text-sm text-[#1C1C1C] bg-white shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all flex items-center">
          Huỷ
        </a>
      </div>
    </form>
  </div>

  <a href="{{ route('client.profile.password') }}"
    class="block bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] px-5 py-4 flex items-center gap-3 hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-xl">🔒</div>
    <div class="flex-1">
      <div class="font-black text-[#1C1C1C] text-sm">Đổi mật khẩu</div>
      <div class="text-xs text-gray-400">Cập nhật mật khẩu bảo mật tài khoản</div>
    </div>
    <span class="text-gray-400">›</span>
  </a>

</div>
@endsection
