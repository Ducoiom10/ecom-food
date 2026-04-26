@extends('layouts.client')
@section('title', 'Đổi mật khẩu')
@section('page_heading', 'Đổi mật khẩu')

@section('content')
<div class="p-4 lg:p-8 max-w-lg mx-auto">

  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('client.profile.edit') }}" class="w-9 h-9 border-2 border-[#1C1C1C] rounded-xl bg-white flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all font-bold">←</a>
    <h1 class="font-black text-[#1C1C1C] text-xl">Đổi mật khẩu</h1>
  </div>

  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-6">

    @if(session('success'))
    <div class="bg-green-50 border-2 border-green-300 rounded-xl px-4 py-3 mb-4 text-green-700 text-sm font-bold">✅ {{ session('success') }}</div>
    @endif

    <form action="{{ route('client.profile.password.update') }}" method="POST" class="space-y-4">
      @csrf @method('POST')

      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">Mật khẩu hiện tại</label>
        <div class="relative">
          <input type="password" name="current_password" id="pwd0" required
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all @error('current_password') border-red-400 @enderror" />
          <button type="button" onclick="togglePwd('pwd0')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">👁</button>
        </div>
        @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">Mật khẩu mới</label>
        <div class="relative">
          <input type="password" name="password" id="pwd1" required
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all @error('password') border-red-400 @enderror" />
          <button type="button" onclick="togglePwd('pwd1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">👁</button>
        </div>
        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        <div class="flex gap-1 mt-2">
          <div class="flex-1 h-1.5 rounded-full bg-gray-200" id="bar1"></div>
          <div class="flex-1 h-1.5 rounded-full bg-gray-200" id="bar2"></div>
          <div class="flex-1 h-1.5 rounded-full bg-gray-200" id="bar3"></div>
          <div class="flex-1 h-1.5 rounded-full bg-gray-200" id="bar4"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1" id="strength-label">Tối thiểu 6 ký tự</p>
      </div>

      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">Xác nhận mật khẩu mới</label>
        <div class="relative">
          <input type="password" name="password_confirmation" id="pwd2" required
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all" />
          <button type="button" onclick="togglePwd('pwd2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">👁</button>
        </div>
      </div>

      <button type="submit"
        class="w-full bg-[#FF6B35] text-white font-black py-3.5 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
        🔒 Cập nhật mật khẩu
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function togglePwd(id) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}
document.getElementById('pwd1').addEventListener('input', function() {
  const val = this.value;
  const colors = ['bg-red-400','bg-orange-400','bg-yellow-400','bg-green-500'];
  const labels = ['Yếu','Trung bình','Khá','Mạnh'];
  let score = 0;
  if (val.length >= 6) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  for (let i = 1; i <= 4; i++) {
    document.getElementById('bar' + i).className = 'flex-1 h-1.5 rounded-full ' + (i <= score ? colors[score-1] : 'bg-gray-200');
  }
  document.getElementById('strength-label').textContent = val.length === 0 ? 'Tối thiểu 6 ký tự' : (labels[score-1] || 'Yếu');
});
</script>
@endpush
