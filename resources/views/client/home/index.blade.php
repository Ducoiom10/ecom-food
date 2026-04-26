@extends('layouts.client')
@section('title', 'Trang chủ')
@section('page_heading', 'Trang chủ')

@section('content')

{{-- Skeleton --}}
<div id="skeleton-home" class="p-4 lg:p-8 max-w-7xl mx-auto">
  <div class="lg:grid lg:grid-cols-3 lg:gap-8">
    <div class="lg:col-span-2 space-y-4">
      <div class="skeleton h-40 lg:h-52 w-full"></div>
      <div class="skeleton h-20 w-full"></div>
      <div class="flex gap-2">@for($i=0;$i<5;$i++)<div class="skeleton h-9 w-20 flex-shrink-0"></div>@endfor</div>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @for($i=0;$i<6;$i++)
        <div class="space-y-2"><div class="skeleton aspect-[4/3] w-full"></div><div class="skeleton h-4 w-3/4"></div><div class="skeleton h-4 w-1/2"></div></div>
        @endfor
      </div>
    </div>
    <div class="hidden lg:block space-y-4">
      <div class="skeleton h-48 w-full"></div>
      <div class="skeleton h-48 w-full"></div>
    </div>
  </div>
</div>

<div id="main-home" class="hidden">
<div class="p-4 lg:p-8 max-w-7xl mx-auto">
  <div class="lg:grid lg:grid-cols-3 lg:gap-8">

    {{-- LEFT --}}
    <div class="lg:col-span-2 space-y-6">
      @include('client.home.partials.hero')
      @include('client.home.partials.mood-filters')
      @include('client.home.partials.categories')
      @include('client.home.partials.best-sellers')
    </div>

    {{-- RIGHT desktop --}}
    <div class="hidden lg:block space-y-6">
      @include('client.home.partials.combos-sidebar')
      @include('client.home.partials.reviews-sidebar')
      @include('client.home.partials.user-stats')
    </div>
  </div>

  {{-- Mobile combos & reviews --}}
  <div class="lg:hidden mt-6 space-y-6">
    @include('client.home.partials.combos-mobile')
    @include('client.home.partials.reviews-mobile')
  </div>
</div>
</div>

@endsection

@push('scripts')
<script>
window.addEventListener('load', () => {
  document.getElementById('skeleton-home').classList.add('hidden');
  document.getElementById('main-home').classList.remove('hidden');
});
function addToCart(productId) {
  fetch('{{ route('client.cart.add') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ product_id: productId, quantity: 1 })
  }).then(r => r.json()).then(data => {
    if (data.ok) {
      const btn = event.target;
      btn.textContent = '✓'; btn.classList.add('bg-green-500','text-white');
      setTimeout(() => { btn.textContent = '+'; btn.classList.remove('bg-green-500','text-white'); }, 1500);
    }
  });
}
</script>
@endpush
