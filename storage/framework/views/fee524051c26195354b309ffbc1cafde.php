<?php $__env->startSection('title', 'Tài khoản'); ?>

<?php $__env->startSection('content'); ?>
<div class="pb-4">

  
  <div class="bg-[#1C1C1C] px-4 pt-6 pb-8 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 bg-[#FF6B35]/20 rounded-full -translate-x-4 -translate-y-4"></div>
    <div class="relative z-10 flex items-center gap-4">
      <div class="w-16 h-16 bg-[#FF6B35] border-2 border-[#FFD23F] rounded-2xl flex items-center justify-center text-2xl shadow-[4px_4px_0px_#FFD23F]">👤</div>
      <div>
        <h2 class="text-white font-black text-xl"><?php echo e($user->name); ?></h2>
        <p class="text-gray-400 text-sm"><?php echo e($user->phone); ?></p>
        <div class="flex items-center gap-2 mt-1">
          <span class="bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-2 py-0.5 rounded-full">🏆 VIP Gold</span>
          <span class="text-gray-500 text-xs">Thành viên từ 01/2026</span>
        </div>
      </div>
    </div>
  </div>

  
  <div class="mx-4 -mt-4 relative z-10">
    <div class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 flex items-center gap-4">
      <div class="w-12 h-12 bg-[#1C1C1C] rounded-xl flex items-center justify-center flex-shrink-0">
        <span class="text-[#FFD23F] text-2xl">⭐</span>
      </div>
      <div class="flex-1">
        <p class="text-[#1C1C1C]/60 text-xs font-bold uppercase">Snack Points</p>
        <p class="font-black text-[#1C1C1C] text-3xl"><?php echo e(number_format($snackPoints ?? 342)); ?></p>
        <p class="text-[#1C1C1C]/70 text-xs mt-0.5">≈ <?php echo e(number_format(($snackPoints ?? 342) * 100)); ?>đ · Đủ dùng 1 bữa miễn phí!</p>
      </div>
      <div class="text-right">
        <span class="text-xs font-black text-green-700">📈 +42 tuần này</span>
      </div>
    </div>
  </div>

  
  <div class="flex px-4 mt-4 gap-2">
    <button onclick="switchTab('orders')" id="tab-orders" class="flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-black bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]">🧾 Lịch sử đơn</button>
    <button onclick="switchTab('loyalty')" id="tab-loyalty" class="flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-black bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">⭐ Phần thưởng</button>
  </div>

  
  <div id="panel-orders" class="px-4 mt-4 space-y-3">
    <?php $__empty_1 = true; $__currentLoopData = $orderHistory ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4">
      <div class="flex items-center justify-between mb-2">
        <div>
          <span class="font-black text-[#1C1C1C] text-sm"><?php echo e($order->order_number); ?></span>
          <div class="text-gray-400 text-xs mt-0.5">🕐 <?php echo e($order->created_at->format('d/m/Y')); ?></div>
        </div>
        <span class="bg-green-100 text-green-600 text-xs font-black px-2 py-0.5 rounded-full border border-green-200">
          <?php echo e($order->status === 'completed' ? '✓ Hoàn thành' : ucfirst($order->status)); ?>

        </span>
      </div>
      <div class="text-xs text-gray-500 mb-3">
        <?php echo e($order->items->map(fn($i) => ($i->product?->name ?? 'Sản phẩm') . ' x' . $i->quantity)->implode(' · ')); ?>

      </div>
      <div class="flex items-center justify-between">
        <span class="font-black text-[#FF6B35]"><?php echo e(number_format($order->grand_total)); ?>đ</span>
        <a href="<?php echo e(route('client.order.show', $order->id)); ?>" class="flex items-center gap-1.5 bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-3 py-2 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">
          📍 Theo dõi
        </a>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="text-center py-8 text-gray-400">
      <div class="text-4xl mb-2">📋</div>
      <p class="font-bold text-sm">Chưa có đơn hàng nào</p>
      <a href="<?php echo e(route('client.menu')); ?>" class="mt-2 inline-block text-[#FF6B35] font-black text-sm hover:underline">Đặt món ngay →</a>
    </div>
    <?php endif; ?>
  </div>

  
  <div id="panel-loyalty" class="px-4 mt-4 space-y-3 hidden">
    <?php $__empty_1 = true; $__currentLoopData = $challenges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $challenge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $progress = $progressMap[$challenge->id] ?? null; ?>
    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 <?php echo e($progress?->isCompleted() ? 'opacity-70' : ''); ?>">
      <div class="flex items-center gap-3">
        <span class="text-2xl">🎯</span>
        <div class="flex-1">
          <div class="flex items-center justify-between">
            <span class="font-black text-[#1C1C1C] text-sm"><?php echo e($challenge->title); ?></span>
            <span class="text-[#FFD23F] font-black text-sm">+<?php echo e($challenge->points_reward); ?> pts</span>
          </div>
          <div class="flex items-center gap-2 mt-1.5">
            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
              <div class="h-full bg-[#FF6B35] rounded-full" style="width: <?php echo e($challenge->target_count > 0 ? min(100, (($progress?->current_count ?? 0) / $challenge->target_count) * 100) : 0); ?>%"></div>
            </div>
            <span class="text-xs text-gray-500"><?php echo e($progress?->current_count ?? 0); ?>/<?php echo e($challenge->target_count); ?></span>
          </div>
        </div>
      </div>
      <?php if($progress?->isCompleted()): ?>
      <div class="mt-2 bg-green-50 border border-green-200 rounded-xl px-3 py-1.5 text-xs text-green-600 font-bold text-center">✓ Đã hoàn thành!</div>
      <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="text-center py-8 text-gray-400">
      <div class="text-4xl mb-2">⭐</div>
      <p class="text-sm">Chưa có thử thách nào.</p>
    </div>
    <?php endif; ?>
  </div>

  
  <div class="px-4 mt-6 space-y-2">
    <?php $__currentLoopData = [
      ['icon' => '🔔', 'label' => 'Thông báo',  'sub' => 'Email & Push notification', 'route' => '#'],
      ['icon' => '🔒', 'label' => 'Bảo mật',    'sub' => 'Đổi mật khẩu, 2FA',         'route' => '#'],
      ['icon' => '❓', 'label' => 'Trợ giúp',   'sub' => 'FAQ, Báo cáo sự cố',        'route' => '#'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e($setting['route']); ?>" class="w-full bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] px-4 py-3 flex items-center gap-3 hover:shadow-[1px_1px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all block">
      <div class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0 text-lg"><?php echo e($setting['icon']); ?></div>
      <div class="flex-1 text-left">
        <div class="font-black text-[#1C1C1C] text-sm"><?php echo e($setting['label']); ?></div>
        <div class="text-xs text-gray-400"><?php echo e($setting['sub']); ?></div>
      </div>
      <span class="text-gray-400">›</span>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <form action="<?php echo e(route('logout')); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <button type="submit" class="w-full bg-red-50 border-2 border-red-200 rounded-2xl px-4 py-3 flex items-center gap-3 hover:bg-red-100 transition-all">
        <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0 text-lg">🚪</div>
        <span class="font-black text-red-500 text-sm">Đăng xuất</span>
      </button>
    </form>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function switchTab(tab) {
  ['orders','loyalty'].forEach(t => {
    document.getElementById('panel-' + t).classList.toggle('hidden', t !== tab);
    const btn = document.getElementById('tab-' + t);
    if (t === tab) btn.className = btn.className.replace('bg-white text-[#1C1C1C]', 'bg-[#FF6B35] text-white');
    else btn.className = btn.className.replace('bg-[#FF6B35] text-white', 'bg-white text-[#1C1C1C]');
  });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ecom-food\resources\views/client/profile/index.blade.php ENDPATH**/ ?>