<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ba Anh Em - @yield('title', 'Trang chủ')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    * { box-sizing: border-box; }
    body { background: #FAFAF8; font-family: 'Segoe UI', sans-serif; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .neo-shadow    { box-shadow: 4px 4px 0px #1C1C1C; }
    .neo-shadow-sm { box-shadow: 2px 2px 0px #1C1C1C; }
    .neo-shadow-orange { box-shadow: 4px 4px 0px #FF6B35; }
    @keyframes shimmer { 0%{background-position:-800px 0} 100%{background-position:800px 0} }
    .skeleton { background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:800px 100%; animation:shimmer 1.5s infinite; border-radius:12px; }
  </style>
  @stack('styles')
</head>
<body class="bg-[#FAFAF8]">

{{-- ===== DESKTOP ===== --}}
<div class="hidden lg:flex min-h-screen">
  @include('layouts.components.sidebar')

  <div class="flex-1 flex flex-col min-w-0">
    <header class="sticky top-0 z-40 bg-white border-b-2 border-[#1C1C1C] px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="font-black text-[#1C1C1C] text-xl">@yield('page_heading', 'Trang chủ')</h1>
        <p class="text-gray-400 text-xs mt-0.5">{{ now()->locale('vi')->isoFormat('dddd, D [tháng] M, YYYY') }}</p>
      </div>
      <div class="flex items-center gap-4">
        @auth
        <a href="{{ route('client.profile') }}" class="flex items-center gap-2 border-2 border-[#1C1C1C] rounded-xl px-3 py-2 bg-white neo-shadow-sm hover:shadow-none transition-all">
          <div class="w-7 h-7 bg-[#FF6B35] rounded-lg flex items-center justify-center text-white text-sm font-black">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </div>
          <span class="font-bold text-sm text-[#1C1C1C]">{{ auth()->user()->name }}</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="font-black text-sm text-[#FF6B35] border-2 border-[#FF6B35] px-4 py-2 rounded-xl hover:bg-orange-50 transition-all">Đăng nhập</a>
        <a href="{{ route('register') }}" class="font-black text-sm bg-[#FF6B35] text-white border-2 border-[#1C1C1C] px-4 py-2 rounded-xl neo-shadow-sm hover:shadow-none transition-all">Đăng ký</a>
        @endauth
      </div>
    </header>
    <main class="flex-1 overflow-y-auto">
      @yield('content')
    </main>
  </div>
</div>

{{-- ===== MOBILE ===== --}}
<div class="lg:hidden flex flex-col min-h-screen">
  @include('layouts.components.header')

  <main class="flex-1 overflow-y-auto pb-24">
    @yield('content')
  </main>

  @auth
  @if(auth()->user()->role !== 'customer')
  <div class="fixed bottom-20 right-4 z-40">
    <a href="{{ route('admin.kds') }}" class="bg-[#1C1C1C] text-white text-xs px-3 py-2 rounded-xl border-2 border-[#1C1C1C] neo-shadow-orange font-bold block">
      🍳 Admin
    </a>
  </div>
  @endif
  @endauth

  @include('layouts.components.footer')
</div>

<script>
document.addEventListener('click', function(e) {
  if (!document.getElementById('mob-branch-wrap')?.contains(e.target))
    document.getElementById('mob-branch-menu')?.classList.add('hidden');
  if (!document.getElementById('desk-branch-wrap')?.contains(e.target))
    document.getElementById('desk-branch-menu')?.classList.add('hidden');
});
function toggleMobBranch(e) { e.stopPropagation(); document.getElementById('mob-branch-menu').classList.toggle('hidden'); }
function toggleDeskBranch() { document.getElementById('desk-branch-menu').classList.toggle('hidden'); }
function selectBranch(type, name, isClosed) {
  if (isClosed) return;
  document.getElementById(type === 'mob' ? 'mob-branch-label' : 'desk-branch-label').textContent = name;
  document.getElementById(type === 'mob' ? 'mob-branch-menu' : 'desk-branch-menu').classList.add('hidden');
}
function setMode(mode) {
  const ids = { delivery:['desk-btn-delivery','mob-btn-delivery'], pickup:['desk-btn-pickup','mob-btn-pickup'] };
  ['delivery','pickup'].forEach(m => {
    ids[m].forEach(id => {
      const el = document.getElementById(id); if (!el) return;
      if (m === mode) { el.classList.add(m==='delivery'?'bg-[#FF6B35]':'bg-[#1C1C1C]','text-white'); el.classList.remove('bg-white','text-[#1C1C1C]'); }
      else { el.classList.remove('bg-[#FF6B35]','bg-[#1C1C1C]','text-white'); el.classList.add('bg-white','text-[#1C1C1C]'); }
    });
  });
}
function openModal(id) { const m=document.getElementById(id); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeModal(id) { const m=document.getElementById(id); m.classList.add('hidden'); m.classList.remove('flex'); }
</script>
@stack('scripts')
</body>
</html>
