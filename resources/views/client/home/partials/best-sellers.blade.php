{{-- Best Sellers Grid --}}
<div>
  <h3 class="font-black text-[#1C1C1C] mb-4 flex items-center gap-2 text-base lg:text-lg">🔥 Bán chạy nhất</h3>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-3 lg:gap-4">
    @forelse($menuItems as $item)
      @include('client.partials.product-card', ['product' => $item])
    @empty
    <div class="col-span-full text-center py-12 text-gray-400">
      <div class="text-5xl mb-3">🍜</div>
      <p class="font-bold">Chưa có món nào. Hãy thêm sản phẩm!</p>
    </div>
    @endforelse
  </div>
</div>
