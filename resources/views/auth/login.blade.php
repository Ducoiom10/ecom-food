<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ba Anh Em - Đăng nhập</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; }
    .neo-shadow { box-shadow: 4px 4px 0px #1C1C1C; }
    .neo-shadow-sm { box-shadow: 2px 2px 0px #1C1C1C; }
    .input-valid   { border-color: #16a34a !important; box-shadow: 2px 2px 0px #16a34a; }
    .input-invalid { border-color: #dc2626 !important; box-shadow: 2px 2px 0px #dc2626; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner { width:18px; height:18px; border:2px solid rgba(255,255,255,.3); border-top-color:white; border-radius:50%; animation:spin .7s linear infinite; display:inline-block; }
  </style>
</head>
<body class="min-h-screen bg-[#FAFAF8] flex items-center justify-center p-4">

<div class="w-full max-w-md">

  {{-- Logo --}}
  <div class="text-center mb-8">
    <a href="{{ route('client.home') }}">
      <div class="w-20 h-20 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-2xl neo-shadow flex items-center justify-center mx-auto mb-4 hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
        <span class="text-4xl">🍜</span>
      </div>
    </a>
    <h1 class="font-black text-[#1C1C1C] text-3xl">Ba Anh Em</h1>
    <p class="text-gray-600 text-sm mt-1">Đăng nhập để đặt món ngon 😋</p>
  </div>

  {{-- Session errors --}}
  @if($errors->any())
  <div class="bg-red-50 border-2 border-red-400 rounded-xl px-4 py-3 mb-4 flex items-center gap-2 text-red-700 text-sm font-bold">
    <span>⚠️</span> {{ $errors->first() }}
  </div>
  @endif

  @if(session('sent'))
  <div class="bg-green-50 border-2 border-green-400 rounded-xl px-4 py-3 mb-4 text-green-700 text-sm font-bold">
    ✅ Đã gửi link đặt lại mật khẩu!
  </div>
  @endif

  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl neo-shadow p-6 lg:p-8">
    <form id="login-form" action="{{ route('login.post') }}" method="POST" class="space-y-5" novalidate>
      @csrf

      {{-- Phone --}}
      <div>
        <label for="phone" class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">
          Số điện thoại <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
            placeholder="0901 234 567" autocomplete="tel"
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none transition-all pr-10 @error('phone') input-invalid @enderror"
            oninput="validatePhone(this)" onblur="validatePhone(this)" />
          <span id="phone-icon" class="absolute right-3 top-1/2 -translate-y-1/2 text-base hidden"></span>
        </div>
        <p id="phone-error" class="text-red-600 text-xs mt-1 font-medium @error('phone') block @else hidden @enderror">
          {{ $errors->first('phone') }}
        </p>
      </div>

      {{-- Password --}}
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label for="password" class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide">
            Mật khẩu <span class="text-red-500">*</span>
          </label>
          <a href="{{ route('auth.forgot') }}" class="text-xs text-[#FF6B35] font-bold hover:underline">Quên mật khẩu?</a>
        </div>
        <div class="relative">
          <input type="password" id="password" name="password"
            placeholder="••••••••" autocomplete="current-password"
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none transition-all pr-20 @error('password') input-invalid @enderror"
            oninput="validatePassword(this)" onblur="validatePassword(this)" />
          <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
            <span id="pwd-icon" class="text-base hidden"></span>
            <button type="button" onclick="togglePwd('password', this)" class="text-gray-400 hover:text-[#1C1C1C] text-sm font-bold transition-colors">Hiện</button>
          </div>
        </div>
        <p id="password-error" class="text-red-600 text-xs mt-1 font-medium @error('password') block @else hidden @enderror">
          {{ $errors->first('password') }}
        </p>
      </div>

      {{-- Remember --}}
      <label class="flex items-center gap-2.5 cursor-pointer group">
        <div class="relative">
          <input type="checkbox" name="remember" id="remember" class="sr-only peer" />
          <div class="w-5 h-5 border-2 border-[#1C1C1C] rounded-md peer-checked:bg-[#FF6B35] peer-checked:border-[#FF6B35] transition-all flex items-center justify-center">
            <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <span class="text-sm text-gray-700 group-hover:text-[#1C1C1C] transition-colors">Ghi nhớ đăng nhập</span>
      </label>

      <button type="submit" id="submit-btn"
        class="w-full bg-[#FF6B35] text-white font-black py-3.5 rounded-xl border-2 border-[#1C1C1C] neo-shadow hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all text-base flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-x-0 disabled:translate-y-0 disabled:shadow-none">
        <span id="btn-text">⚡ Đăng nhập</span>
        <span id="btn-spinner" class="spinner hidden"></span>
      </button>
    </form>
  </div>

  <p class="text-center text-sm text-gray-600 mt-5">
    Chưa có tài khoản?
    <a href="{{ route('register') }}" class="text-[#FF6B35] font-black hover:underline">Đăng ký ngay →</a>
  </p>
  <p class="text-center mt-2">
    <a href="{{ route('client.home') }}" class="text-xs text-gray-500 hover:text-[#1C1C1C] transition-colors">← Về trang chủ</a>
  </p>

</div>

<script>
function togglePwd(id, btn) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
  btn.textContent = el.type === 'text' ? 'Ẩn' : 'Hiện';
}

function setInputState(input, iconId, errorId, isValid, errorMsg) {
  input.classList.remove('input-valid','input-invalid');
  document.getElementById(iconId).classList.add('hidden');
  document.getElementById(errorId).classList.add('hidden');
  if (!input.value) return;
  if (isValid) {
    input.classList.add('input-valid');
    document.getElementById(iconId).textContent = '✅';
    document.getElementById(iconId).classList.remove('hidden');
  } else {
    input.classList.add('input-invalid');
    document.getElementById(iconId).textContent = '❌';
    document.getElementById(iconId).classList.remove('hidden');
    document.getElementById(errorId).textContent = errorMsg;
    document.getElementById(errorId).classList.remove('hidden');
  }
}

function validatePhone(input) {
  const ok = /^(0|\+84)[0-9]{8,9}$/.test(input.value.replace(/\s/g,''));
  setInputState(input, 'phone-icon', 'phone-error', ok, 'Số điện thoại không hợp lệ');
}

function validatePassword(input) {
  const ok = input.value.length >= 6;
  setInputState(input, 'pwd-icon', 'password-error', ok, 'Mật khẩu tối thiểu 6 ký tự');
}

document.getElementById('login-form').addEventListener('submit', function(e) {
  const phone = document.getElementById('phone').value;
  const pwd   = document.getElementById('password').value;
  if (!phone || !pwd) { e.preventDefault(); return; }
  document.getElementById('btn-text').textContent = 'Đang đăng nhập...';
  document.getElementById('btn-spinner').classList.remove('hidden');
  document.getElementById('submit-btn').disabled = true;
});
</script>
</body>
</html>
