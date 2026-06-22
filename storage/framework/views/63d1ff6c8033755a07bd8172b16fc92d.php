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
    /* Input states */
    .input-valid   { border-color: #16a34a !important; box-shadow: 2px 2px 0px #16a34a; }
    .input-invalid { border-color: #dc2626 !important; box-shadow: 2px 2px 0px #dc2626; }
    /* Loading spinner */
    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner { width:18px; height:18px; border:2px solid rgba(255,255,255,.3); border-top-color:white; border-radius:50%; animation:spin .7s linear infinite; display:inline-block; }
  </style>
</head>
<body class="min-h-screen bg-[#FAFAF8] flex items-center justify-center p-4">

<div class="w-full max-w-md">

  
  <div class="text-center mb-8">
    <a href="<?php echo e(route('client.home')); ?>">
      <div class="w-20 h-20 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-2xl neo-shadow flex items-center justify-center mx-auto mb-4 hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
        <span class="text-4xl">🍜</span>
      </div>
    </a>
    <h1 class="font-black text-[#1C1C1C] text-3xl">Ba Anh Em</h1>
    <p class="text-gray-600 text-sm mt-1">Đăng nhập để đặt món ngon 😋</p>
  </div>

  
  <?php if(session('error')): ?>
  <div class="bg-red-50 border-2 border-red-400 rounded-xl px-4 py-3 mb-4 flex items-center gap-2 text-red-700 text-sm font-bold">
    <span class="text-lg">⚠️</span> <?php echo e(session('error')); ?>

  </div>
  <?php endif; ?>

  
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl neo-shadow p-6 lg:p-8">
    <form id="login-form" action="<?php echo e(route('login.post')); ?>" method="POST" class="space-y-5" novalidate>
      <?php echo csrf_field(); ?>

      
      <div>
        <label for="phone" class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">
          Số điện thoại <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input type="tel" id="phone" name="phone" value="<?php echo e(old('phone')); ?>"
            placeholder="0901 234 567" autocomplete="tel"
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none transition-all pr-10
                   <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            oninput="validatePhone(this)" onblur="validatePhone(this)" />
          <span id="phone-icon" class="absolute right-3 top-1/2 -translate-y-1/2 text-base hidden"></span>
        </div>
        <p id="phone-error" class="text-red-600 text-xs mt-1 font-medium hidden">
          <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </p>
      </div>

      
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label for="password" class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide">
            Mật khẩu <span class="text-red-500">*</span>
          </label>
          <a href="<?php echo e(route('auth.forgot')); ?>" class="text-xs text-[#FF6B35] font-bold hover:underline">Quên mật khẩu?</a>
        </div>
        <div class="relative">
          <input type="password" id="password" name="password"
            placeholder="••••••••" autocomplete="current-password"
            class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none transition-all pr-20
                   <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            oninput="validatePassword(this)" onblur="validatePassword(this)" />
          <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
            <span id="pwd-icon" class="text-base hidden"></span>
            <button type="button" onclick="togglePwd('password', this)"
              class="text-gray-400 hover:text-[#1C1C1C] transition-colors text-sm font-bold">
              Hiện
            </button>
          </div>
        </div>
        <p id="password-error" class="text-red-600 text-xs mt-1 font-medium hidden">
          <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><?php echo e($message); ?><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </p>
      </div>

      
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

    
    <div class="flex items-center gap-3 my-5">
      <div class="flex-1 h-px bg-gray-200"></div>
      <span class="text-xs text-gray-500 font-medium">hoặc đăng nhập với</span>
      <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    
    <div class="grid grid-cols-2 gap-3">
      <button class="flex items-center justify-center gap-2 py-2.5 border-2 border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:border-[#1C1C1C] hover:bg-gray-50 transition-all neo-shadow-sm hover:shadow-none">
        <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
        Google
      </button>
      <button class="flex items-center justify-center gap-2 py-2.5 border-2 border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:border-[#1C1C1C] hover:bg-gray-50 transition-all neo-shadow-sm hover:shadow-none">
        <svg class="w-4 h-4" fill="#1877F2" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        Facebook
      </button>
    </div>
  </div>

  
  <p class="text-center text-sm text-gray-600 mt-5">
    Chưa có tài khoản?
    <a href="<?php echo e(route('register')); ?>" class="text-[#FF6B35] font-black hover:underline">Đăng ký ngay →</a>
  </p>
  <p class="text-center mt-2">
    <a href="<?php echo e(route('client.home')); ?>" class="text-xs text-gray-500 hover:text-[#1C1C1C] transition-colors">← Về trang chủ</a>
  </p>

</div>

<script>
function togglePwd(id, btn) {
  const el = document.getElementById(id);
  const isHidden = el.type === 'password';
  el.type = isHidden ? 'text' : 'password';
  btn.textContent = isHidden ? 'Ẩn' : 'Hiện';
}

function setInputState(input, iconEl, errorEl, isValid, errorMsg) {
  input.classList.remove('input-valid', 'input-invalid');
  iconEl.classList.add('hidden');
  errorEl.classList.add('hidden');
  if (input.value === '') return;
  if (isValid) {
    input.classList.add('input-valid');
    iconEl.textContent = '✅';
    iconEl.classList.remove('hidden');
  } else {
    input.classList.add('input-invalid');
    iconEl.textContent = '❌';
    iconEl.classList.remove('hidden');
    errorEl.textContent = errorMsg;
    errorEl.classList.remove('hidden');
  }
}

function validatePhone(input) {
  const ok = /^(0|\+84)[0-9]{8,9}$/.test(input.value.replace(/\s/g,''));
  setInputState(input, document.getElementById('phone-icon'), document.getElementById('phone-error'),
    ok, 'Số điện thoại không hợp lệ');
}

function validatePassword(input) {
  const ok = input.value.length >= 6;
  setInputState(input, document.getElementById('pwd-icon'), document.getElementById('password-error'),
    ok, 'Mật khẩu tối thiểu 6 ký tự');
}

// Show lỗi từ Laravel ngay khi load
<?php $phoneErr = $errors->first('phone'); $pwdErr = $errors->first('password'); ?>
<?php if($phoneErr): ?>
  document.getElementById('phone').classList.add('input-invalid');
  document.getElementById('phone-error').textContent = '<?php echo e($phoneErr); ?>';
  document.getElementById('phone-error').classList.remove('hidden');
<?php endif; ?>
<?php if($pwdErr): ?>
  document.getElementById('password').classList.add('input-invalid');
  document.getElementById('password-error').textContent = '<?php echo e($pwdErr); ?>';
  document.getElementById('password-error').classList.remove('hidden');
<?php endif; ?>

// Loading state khi submit
document.getElementById('login-form').addEventListener('submit', function(e) {
  const phone = document.getElementById('phone').value;
  const pwd = document.getElementById('password').value;
  if (!phone || !pwd) { e.preventDefault(); return; }
  const btn = document.getElementById('submit-btn');
  document.getElementById('btn-text').textContent = 'Đang đăng nhập...';
  document.getElementById('btn-spinner').classList.remove('hidden');
  btn.disabled = true;
});
</script>
</body>
</html>
<?php /**PATH C:\laragon\www\ecom-food\resources\views/auth/login.blade.php ENDPATH**/ ?>