<?php $__env->startSection('title', 'Trang chủ'); ?>
<?php $__env->startSection('page_heading', 'Trang chủ'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  @keyframes shimmer { 0%{background-position:-800px 0} 100%{background-position:800px 0} }
  .skeleton { background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%); background-size:800px 100%; animation:shimmer 1.5s infinite; border-radius:12px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div id="skeleton-home" class="p-4 lg:p-8 max-w-7xl mx-auto">
  <div class="lg:grid lg:grid-cols-3 lg:gap-8">
    <div class="lg:col-span-2 space-y-4">
      <div class="skeleton h-40 lg:h-52 w-full"></div>
      <div class="skeleton h-20 w-full"></div>
      <div class="flex gap-2"><?php for($i=0;$i<5;$i++): ?><div class="skeleton h-9 w-20 flex-shrink-0"></div><?php endfor; ?></div>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <?php for($i=0;$i<6;$i++): ?>
        <div class="space-y-2"><div class="skeleton aspect-[4/3] w-full"></div><div class="skeleton h-4 w-3/4"></div><div class="skeleton h-4 w-1/2"></div></div>
        <?php endfor; ?>
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

    
    <div class="lg:col-span-2 space-y-6">

      
      <div class="relative overflow-hidden bg-[#1C1C1C] border-2 border-[#1C1C1C] rounded-2xl neo-shadow-orange p-6 lg:p-8">
        <div class="relative z-10 max-w-sm">
          <div class="flex items-center gap-2 mb-3">
            <span class="bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-2 py-0.5 rounded-full border border-[#1C1C1C]">🔥 HOT DEAL</span>
            <span class="text-white/60 text-xs">Hôm nay thôi!</span>
          </div>
          <h2 class="text-white text-2xl lg:text-3xl font-black mb-2 leading-tight">Combo Trưa<br/>Văn Phòng 🏢</h2>
          <p class="text-white/70 text-sm mb-5">Mì trộn + Trà sữa chỉ còn <span class="text-[#FFD23F] font-black text-lg">65.000đ</span></p>
          <a href="<?php echo e(route('client.menu')); ?>" class="inline-flex items-center gap-2 bg-[#FF6B35] text-white font-black px-5 py-2.5 rounded-xl border-2 border-[#FF6B35] shadow-[2px_2px_0px_white] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
            Đặt ngay →
          </a>
        </div>
        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-7xl lg:text-9xl opacity-10 select-none">🍜</div>
      </div>

      
      <a href="<?php echo e(route('client.group-order')); ?>" class="block w-full bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl neo-shadow p-4 lg:p-5 flex items-center gap-4 hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-[#1C1C1C] rounded-xl flex items-center justify-center flex-shrink-0">
          <span class="text-[#FFD23F] text-2xl lg:text-3xl">👥</span>
        </div>
        <div class="flex-1">
          <div class="font-black text-[#1C1C1C] text-base lg:text-lg flex items-center gap-2">
            Đặt đơn nhóm
            <span class="bg-[#FF6B35] text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">NEW</span>
          </div>
          <p class="text-xs lg:text-sm text-[#1C1C1C]/70 mt-0.5">Tạo phòng, gửi link, mỗi người tự chọn — chia bill tự động!</p>
        </div>
        <span class="text-[#1C1C1C] text-xl">›</span>
      </a>

      
      <div>
        <h3 class="font-black text-[#1C1C1C] mb-3 flex items-center gap-2 text-base lg:text-lg">⚡ Hôm nay muốn ăn gì?</h3>
        <div class="flex gap-2 flex-wrap">
          <?php $__currentLoopData = [
            ['label'=>'Cần ngọt','emoji'=>'🧁','color'=>'bg-pink-100 border-pink-300 text-pink-700'],
            ['label'=>'Cay xè',  'emoji'=>'🌶️','color'=>'bg-red-100 border-red-300 text-red-700'],
            ['label'=>'Healthy', 'emoji'=>'🥗','color'=>'bg-green-100 border-green-300 text-green-700'],
            ['label'=>'No bụng', 'emoji'=>'💪','color'=>'bg-blue-100 border-blue-300 text-blue-700'],
            ['label'=>'Mau nào', 'emoji'=>'⚡','color'=>'bg-yellow-100 border-yellow-300 text-yellow-700'],
          ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mood): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('client.menu', ['mood' => $mood['label']])); ?>"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 text-xs lg:text-sm font-bold <?php echo e($mood['color']); ?> hover:scale-105 transition-transform">
            <?php echo e($mood['emoji']); ?> <?php echo e($mood['label']); ?>

          </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      
      <div class="flex gap-2 flex-wrap">
        <?php $__currentLoopData = [
          ['id'=>'all',    'label'=>'Tất cả', 'emoji'=>'🍽️'],
          ['id'=>'noodles','label'=>'Mì & Phở','emoji'=>'🍜'],
          ['id'=>'rice',   'label'=>'Cơm',    'emoji'=>'🍚'],
          ['id'=>'snacks', 'label'=>'Ăn vặt', 'emoji'=>'🍗'],
          ['id'=>'drinks', 'label'=>'Đồ uống','emoji'=>'🧋'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('client.menu', ['category' => $cat['id']])); ?>"
          class="flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs lg:text-sm font-bold transition-all
                 <?php echo e(request('category','all') === $cat['id'] ? 'bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]' : 'bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none'); ?>">
          <?php echo e($cat['emoji']); ?> <?php echo e($cat['label']); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      
      <div>
        <h3 class="font-black text-[#1C1C1C] mb-4 flex items-center gap-2 text-base lg:text-lg">🔥 Bán chạy nhất</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-3 lg:gap-4">
          <?php $__currentLoopData = $menuItems ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl neo-shadow overflow-hidden hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all group">
            <a href="<?php echo e(route('client.product', $item->id)); ?>" class="block">
              <div class="relative overflow-hidden" style="padding-top: 66%">
                <img src="<?php echo e($item->image); ?>" alt="<?php echo e($item->name); ?>"
                  class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                <?php if($item->is_new): ?>
                <div class="absolute top-2 left-2 bg-[#FFD23F] border border-[#1C1C1C] text-[#1C1C1C] text-[9px] font-black px-1.5 py-0.5 rounded-full">✨ NEW</div>
                <?php endif; ?>
                <?php if($item->is_best_seller): ?>
                <div class="absolute top-2 right-2 bg-[#FF6B35] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">🔥 TOP</div>
                <?php endif; ?>
              </div>
              <div class="p-3">
                <div class="font-black text-[#1C1C1C] text-sm leading-tight line-clamp-2"><?php echo e($item->name); ?></div>
                <div class="flex items-center gap-1 mt-1 text-[10px] text-gray-500">
                  <span>⭐ <?php echo e($item->category->name ?? ''); ?></span>
                </div>
              </div>
            </a>
            <div class="px-3 pb-3 flex items-center justify-between">
              <span class="font-black text-[#FF6B35]"><?php echo e(number_format($item->base_price)); ?>đ</span>
              <button onclick="addToCart('<?php echo e($item->id); ?>')"
                class="w-8 h-8 rounded-lg border-2 border-[#1C1C1C] bg-[#FFD23F] flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none transition-all text-lg font-bold">+</button>
            </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

    </div>

    
    <div class="hidden lg:block space-y-6">

      
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl neo-shadow overflow-hidden">
        <div class="bg-[#1C1C1C] px-4 py-3 flex items-center justify-between">
          <h3 class="text-white font-black flex items-center gap-2">📈 Combo tiết kiệm</h3>
          <a href="<?php echo e(route('client.menu')); ?>" class="text-[#FFD23F] text-xs font-bold">Xem thêm ›</a>
        </div>
        <div class="p-4 space-y-3">
          <?php $__currentLoopData = $combos ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $combo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('client.menu')); ?>" class="flex gap-3 hover:bg-orange-50 rounded-xl p-2 transition-all block">
            <div class="relative flex-shrink-0">
              <img src="<?php echo e($combo->image); ?>" alt="<?php echo e($combo->name); ?>" class="w-16 h-16 object-cover rounded-xl border-2 border-[#1C1C1C]" />
              <?php if($combo->original_price > $combo->combo_price): ?>
              <div class="absolute -top-1 -right-1 bg-[#FF6B35] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full border border-white">
                -<?php echo e(number_format($combo->original_price - $combo->combo_price)); ?>đ
              </div>
              <?php endif; ?>
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-black text-[#1C1C1C] text-sm"><?php echo e($combo->name); ?></div>
              <div class="text-xs text-gray-500 mt-0.5"><?php echo e($combo->description); ?></div>
              <div class="flex items-center gap-2 mt-1">
                <span class="font-black text-[#FF6B35] text-sm"><?php echo e(number_format($combo->combo_price)); ?>đ</span>
                <span class="text-xs text-gray-400 line-through"><?php echo e(number_format($combo->original_price)); ?>đ</span>
                <span class="bg-green-100 text-green-700 text-[10px] font-black px-1.5 py-0.5 rounded-full">-<?php echo e(number_format($combo->original_price - $combo->combo_price)); ?>đ</span>
              </div>
            </div>
          </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      
      <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl neo-shadow overflow-hidden">
        <div class="bg-[#1C1C1C] px-4 py-3">
          <h3 class="text-white font-black">⭐ Khách hàng nói gì?</h3>
        </div>
        <div class="p-4 space-y-3">
          <?php $__currentLoopData = $reviews ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
            <div class="flex items-center gap-2 mb-1.5">
              <div class="w-7 h-7 bg-[#FF6B35] rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"><?php echo e($review['avatar']); ?></div>
              <div>
                <div class="font-bold text-xs text-[#1C1C1C]"><?php echo e($review['user']); ?></div>
                <div class="text-[#FFD23F] text-xs"><?php echo e(str_repeat('⭐', $review['rating'])); ?></div>
              </div>
            </div>
            <p class="text-xs text-gray-600 leading-relaxed">"<?php echo e($review['comment']); ?>"</p>
            <div class="text-[10px] text-gray-400 mt-1"><?php echo e($review['item']); ?> · <?php echo e($review['time']); ?></div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      
      <div class="bg-[#1C1C1C] border-2 border-[#1C1C1C] rounded-2xl neo-shadow-orange p-4">
        <h3 class="text-white font-black mb-3 text-sm">📊 Hôm nay</h3>
        <div class="grid grid-cols-2 gap-3">
          <?php $__currentLoopData = [['label'=>'Đơn đã giao','value'=>'234','color'=>'text-green-400'],['label'=>'Đang giao','value'=>'12','color'=>'text-orange-400'],['label'=>'Đánh giá TB','value'=>'4.8⭐','color'=>'text-yellow-400'],['label'=>'Doanh thu','value'=>'15.6M','color'=>'text-blue-400']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="bg-white/10 rounded-xl p-3 text-center">
            <div class="font-black text-lg <?php echo e($stat['color']); ?>"><?php echo e($stat['value']); ?></div>
            <div class="text-white/60 text-[10px] mt-0.5"><?php echo e($stat['label']); ?></div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

    </div>
  </div>

  
  <div class="lg:hidden mt-6 space-y-6">
    
    <div>
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-black text-[#1C1C1C] flex items-center gap-2">📈 Combo tiết kiệm</h3>
        <a href="<?php echo e(route('client.menu')); ?>" class="text-[#FF6B35] text-xs font-bold">Xem thêm ›</a>
      </div>
      <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
        <?php $__currentLoopData = $combos ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $combo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('client.menu')); ?>" class="flex-shrink-0 w-48 bg-white border-2 border-[#1C1C1C] rounded-2xl neo-shadow overflow-hidden block">
          <div class="h-24 overflow-hidden relative">
            <img src="<?php echo e($combo->image); ?>" alt="<?php echo e($combo->name); ?>" class="w-full h-full object-cover" />
            <?php if($combo->original_price > $combo->combo_price): ?>
            <div class="absolute top-2 left-2 bg-[#FF6B35] text-white text-[10px] font-black px-2 py-0.5 rounded-full border border-white shadow">
              Tiết kiệm <?php echo e(number_format($combo->original_price - $combo->combo_price)); ?>đ
            </div>
            <?php endif; ?>
          </div>
          <div class="p-3">
            <div class="font-black text-[#1C1C1C] text-xs"><?php echo e($combo->name); ?></div>
            <div class="flex items-center gap-1 mt-1">
              <span class="font-black text-[#FF6B35] text-sm"><?php echo e(number_format($combo->combo_price)); ?>đ</span>
              <span class="text-[10px] text-gray-400 line-through"><?php echo e(number_format($combo->original_price)); ?>đ</span>
            </div>
          </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

    
    <div>
      <h3 class="font-black text-[#1C1C1C] mb-3">⭐ Khách hàng nói gì?</h3>
      <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
        <?php $__currentLoopData = $reviews ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex-shrink-0 w-64 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-[#FF6B35] rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"><?php echo e($review['avatar']); ?></div>
            <div>
              <div class="font-bold text-xs text-[#1C1C1C]"><?php echo e($review['user']); ?></div>
              <div class="text-[#FFD23F] text-xs"><?php echo e(str_repeat('⭐', $review['rating'])); ?></div>
            </div>
          </div>
          <p class="text-xs text-gray-600 leading-relaxed">"<?php echo e($review['comment']); ?>"</p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </div>

</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
window.addEventListener('load', () => {
  document.getElementById('skeleton-home').classList.add('hidden');
  document.getElementById('main-home').classList.remove('hidden');
});
function addToCart(id) {
  fetch('/cart/add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
    body: JSON.stringify({ product_id: id, quantity: 1 })
  }).then(r => r.json()).then(() => {
    if (typeof showToast === 'function') showToast('Đã thêm vào giỏ hàng 🛒', 'success');
  });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ecom-food\resources\views/client/home/index.blade.php ENDPATH**/ ?>