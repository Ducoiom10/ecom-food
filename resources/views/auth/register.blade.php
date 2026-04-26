<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ba Anh Em - Đăng ký</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; }
    .neo-shadow { box-shadow: 4px 4px 0px #1C1C1C; }
    .input-valid   { border-color: #16a34a !important; box-shadow: 2px 2px 0px #16a34a; }
    .input-invalid { border-color: #dc2626 !important; box-shadow: 2px 2px 0px #dc2626; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner { width:18px; height:18px; border:2px solid rgba(255,255,255,.3); border-top-color:white; border-radius:50%; animation:spin .7s linear infinite; display:inline-block; }
  </style>
</head>
<body class="min-h-screen bg-[#FAFAF8] flex items-center justify-center p-4 py-8">

<div class="w-full max-w-md">

  <div class="text-center mb-6">
    <a href="{{ route('client.home') }}">
      <div class="w-16 h-16 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl neo-shadow flex items-center justify-center mx-auto mb-3 hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
        <span class="text-3xl">🍜</span>
      </div>
    </a>
    <h1 class="font-black text-[#1C1C1C] text-2xl">Tạo tài khoản</h1>
    <p class="text-gray-600 text-sm mt-1">Tham gia Ba Anh Em để nhận ưu đãi 🎉</p>
  </div>

  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl neo-shadow p-6">
    <form id="reg-form" action="{{ route('register.post') }}" method="POST" class="space-y-4" novalidate>
      @csrf

      {{-- Name --}}
      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">
          Họ và tên <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" id="reg_name" value="{{ old('name') }}"
          placeholder="Nguyễn Minh Tuấn" required
          class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none transition-all @error('name') input-invalid @enderror"
          onblur="validateRequired(this, 'name_err', 'Vui lòng nhập họ tên')" />
        <p id="name_err" class="text-red-600 text-xs mt-1 @error('name') block @else hidden @enderror">{{ $errors->first('name') }}</p>
      </div>

      {{-- Phone --}}
      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">
          Số điện thoại <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input type="tel" name="phone" id="reg_phone" value="{{ old('phone') }}"
            placeholder="0901 234 567" required
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none transition-all pr-10 @error('phone') input-invalid @enderror"
            oninput="validatePhoneField()" onblur="validatePhoneField()" />
          <span id="phone_icon" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm hidden"></span>
        </div>
        <p id="phone_err" class="text-red-600 text-xs mt-1 @error('phone') block @else hidden @enderror">{{ $errors->first('phone') }}</p>
      </div>

      {{-- Email (optional) --}}
      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">
          Email <span class="text-gray-400 font-normal normal-case">(tuỳ chọn)</span>
        </label>
        <input type="email" name="email" id="reg_email" value="{{ old('email') }}"
          placeholder="minhtuan@email.com"
          class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none transition-all @error('email') input-invalid @enderror" />
        <p class="text-red-600 text-xs mt-1 @error('email') block @else hidden @enderror">{{ $errors->first('email') }}</p>
      </div>

      {{-- Password --}}
      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">
          Mật khẩu <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input type="password" name="password" id="reg_pwd"
            placeholder="Tối thiểu 6 ký tự" required
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none transition-all pr-20 @error('password') input-invalid @enderror"
            oninput="checkStrength(); validatePwdMatch()" />
          <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
            <span id="reg_pwd_icon" class="text-sm hidden"></span>
            <button type="button" onclick="togglePwd('reg_pwd', this)" class="text-gray-400 hover:text-[#1C1C1C] text-xs font-bold">Hiện</button>
          </div>
        </div>
        {{-- Strength --}}
        <div class="flex gap-1 mt-2">
          <div class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all" id="bar1"></div>
          <div class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all" id="bar2"></div>
          <div class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all" id="bar3"></div>
          <div class="flex-1 h-1.5 rounded-full bg-gray-200 transition-all" id="bar4"></div>
        </div>
        <p id="strength_label" class="text-xs text-gray-400 mt-1">Tối thiểu 6 ký tự</p>
        <p id="reg_pwd_err" class="text-red-600 text-xs mt-1 @error('password') block @else hidden @enderror">{{ $errors->first('password') }}</p>
      </div>

      {{-- Confirm password --}}
      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">
          Xác nhận mật khẩu <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input type="password" name="password_confirmation" id="reg_pwd2"
            placeholder="Nhập lại mật khẩu" required
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none transition-all pr-20"
            oninput="validatePwdMatch()" onblur="validatePwdMatch()" />
          <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1">
            <span id="reg_pwd2_icon" class="text-sm hidden"></span>
            <button type="button" onclick="togglePwd('reg_pwd2', this)" class="text-gray-400 hover:text-[#1C1C1C] text-xs font-bold">Hiện</button>
          </div>
        </div>
        <p id="reg_pwd2_err" class="text-red-600 text-xs mt-1 hidden"></p>
      </div>

      {{-- Terms --}}
      <label class="flex items-start gap-2.5 cursor-pointer">
        <div class="relative mt-0.5 flex-shrink-0">
          <input type="checkbox" name="agree" id="agree" required class="sr-only peer" />
          <div class="w-5 h-5 border-2 border-[#1C1C1C] rounded-md peer-checked:bg-[#FF6B35] peer-checked:border-[#FF6B35] transition-all flex items-center justify-center">
            <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <span class="text-sm text-gray-700 leading-relaxed">
          Tôi đồng ý với <a href="#" class="text-[#FF6B35] font-bold hover:underline">Điều khoản dịch vụ</a>
          và <a href="#" class="text-[#FF6B35] font-bold hover:underline">Chính sách bảo mật</a>
        </span>
      </label>

      <button type="submit" id="reg-btn"
        class="w-full bg-[#FF6B35] text-white font-black py-3.5 rounded-xl border-2 border-[#1C1C1C] neo-shadow hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all text-base flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-x-0 disabled:translate-y-0 disabled:shadow-none">
        <span id="reg-btn-text">🎉 Tạo tài khoản</span>
        <span id="reg-spinner" class="spinner hidden"></span>
      </button>
    </form>
  </div>

  <p class="text-center text-sm text-gray-600 mt-5">
    Đã có tài khoản?
    <a href="{{ route('login') }}" class="text-[#FF6B35] font-black hover:underline">Đăng nhập →</a>
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

function setField(inputId, iconId, errId, isValid, errMsg) {
  const input = document.getElementById(inputId);
  input.classList.remove('input-valid','input-invalid');
  document.getElementById(iconId).classList.add('hidden');
  document.getElementById(errId).classList.add('hidden');
  if (!input.value) return;
  if (isValid) {
    input.classList.add('input-valid');
    document.getElementById(iconId).textContent = '✅';
    document.getElementById(iconId).classList.remove('hidden');
  } else {
    input.classList.add('input-invalid');
    document.getElementById(iconId).textContent = '❌';
    document.getElementById(iconId).classList.remove('hidden');
    document.getElementById(errId).textContent = errMsg;
    document.getElementById(errId).classList.remove('hidden');
  }
}

function validateRequired(input, errId, msg) {
  const ok = input.value.trim().length > 0;
  input.classList.toggle('input-invalid', !ok);
  input.classList.toggle('input-valid', ok);
  const err = document.getElementById(errId);
  err.textContent = msg;
  err.classList.toggle('hidden', ok);
}

function validatePhoneField() {
  const val = document.getElementById('reg_phone').value.replace(/\s/g,'');
  const ok = /^(0|\+84)[0-9]{8,9}$/.test(val);
  setField('reg_phone', 'phone_icon', 'phone_err', ok, 'Số điện thoại không hợp lệ (VD: 0901234567)');
}

function checkStrength() {
  const val = document.getElementById('reg_pwd').value;
  const colors = ['bg-red-400','bg-orange-400','bg-yellow-400','bg-green-500'];
  const labels = ['Yếu','Trung bình','Khá tốt','Mạnh 💪'];
  let score = 0;
  if (val.length >= 6) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  for (let i = 1; i <= 4; i++) {
    document.getElementById('bar' + i).className = 'flex-1 h-1.5 rounded-full transition-all ' + (i <= score ? colors[score-1] : 'bg-gray-200');
  }
  const lbl = document.getElementById('strength_label');
  if (!val) { lbl.textContent = 'Tối thiểu 6 ký tự'; lbl.className = 'text-xs text-gray-400 mt-1'; return; }
  lbl.textContent = labels[score-1] || 'Yếu';
  lbl.className = 'text-xs mt-1 font-medium ' + ['text-red-500','text-orange-500','text-yellow-600','text-green-600'][score-1];
}

function validatePwdMatch() {
  const p1 = document.getElementById('reg_pwd').value;
  const p2 = document.getElementById('reg_pwd2').value;
  if (!p2) return;
  const ok = p1 === p2;
  const input = document.getElementById('reg_pwd2');
  input.classList.toggle('input-valid', ok);
  input.classList.toggle('input-invalid', !ok);
  document.getElementById('reg_pwd2_icon').textContent = ok ? '✅' : '❌';
  document.getElementById('reg_pwd2_icon').classList.toggle('hidden', !p2);
  document.getElementById('reg_pwd2_err').textContent = 'Mật khẩu không khớp';
  document.getElementById('reg_pwd2_err').classList.toggle('hidden', ok);
}

document.getElementById('reg-form').addEventListener('submit', function(e) {
  if (!document.getElementById('agree').checked) {
    e.preventDefault();
    alert('Vui lòng đồng ý với điều khoản dịch vụ');
    return;
  }
  document.getElementById('reg-btn-text').textContent = 'Đang tạo tài khoản...';
  document.getElementById('reg-spinner').classList.remove('hidden');
  document.getElementById('reg-btn').disabled = true;
});
</script>
</body>
</html>
