<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ba Anh Em - Quên mật khẩu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; }
    .neo-shadow { box-shadow: 4px 4px 0px #1C1C1C; }
  </style>
</head>
<body class="min-h-screen bg-[#FAFAF8] flex items-center justify-center p-4">

<div class="w-full max-w-md">

  <div class="text-center mb-8">
    <div class="w-16 h-16 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl neo-shadow flex items-center justify-center mx-auto mb-4 text-3xl">🔑</div>
    <h1 class="font-black text-[#1C1C1C] text-2xl">Quên mật khẩu?</h1>
    <p class="text-gray-600 text-sm mt-1">Nhập số điện thoại để nhận hướng dẫn đặt lại</p>
  </div>

  @if(session('sent'))
  <div class="bg-green-50 border-2 border-green-400 rounded-xl px-4 py-3 mb-4 text-green-700 text-sm font-bold">
    ✅ Đã gửi hướng dẫn đặt lại mật khẩu qua SMS!
  </div>
  @endif

  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl neo-shadow p-6">
    <form action="{{ route('auth.forgot.post') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-black text-[#1C1C1C] uppercase tracking-wide mb-1.5">
          Số điện thoại <span class="text-red-500">*</span>
        </label>
        <input type="tel" name="phone" placeholder="0901 234 567" required
          class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm outline-none focus:border-[#FF6B35] transition-all @error('phone') border-red-400 @enderror" />
        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <button type="submit"
        class="w-full bg-[#FF6B35] text-white font-black py-3.5 rounded-xl border-2 border-[#1C1C1C] neo-shadow hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
        📱 Gửi hướng dẫn đặt lại
      </button>
    </form>
  </div>

  <p class="text-center mt-5">
    <a href="{{ route('login') }}" class="text-sm text-[#FF6B35] font-bold hover:underline">← Quay lại đăng nhập</a>
  </p>

</div>
</body>
</html>
