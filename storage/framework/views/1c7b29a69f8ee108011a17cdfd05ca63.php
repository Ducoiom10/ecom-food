<?php $__env->startSection('title', 'Theo dõi đơn hàng #' . $order->order_number); ?>
<?php $__env->startSection('page_heading', 'Theo dõi đơn hàng'); ?>

<?php $__env->startSection('content'); ?>
<?php
$steps = [
    ['key' => 'pending',   'label' => 'Đã đặt',      'icon' => '📋', 'time' => $order->created_at],
    ['key' => 'confirmed', 'label' => 'Xác nhận',    'icon' => '✅', 'time' => $order->confirmed_at],
    ['key' => 'preparing', 'label' => 'Đang làm',    'icon' => '👨‍🍳', 'time' => $order->preparing_at],
    ['key' => 'ready',     'label' => 'Sẵn sàng',    'icon' => '🎉', 'time' => $order->ready_at],
    ['key' => 'completed', 'label' => $order->delivery_mode === 'delivery' ? 'Đã giao' : 'Đã lấy', 'icon' => $order->delivery_mode === 'delivery' ? '🛵' : '🏪', 'time' => $order->completed_at],
];
$statusOrder = ['pending','confirmed','preparing','ready','completed','cancelled'];
$currentIdx  = array_search($order->status, $statusOrder);
?>

<div class="p-4 lg:p-8 max-w-3xl mx-auto space-y-4">

  
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="text-xs text-gray-400 font-medium">Mã đơn hàng</p>
        <p class="font-black text-[#1C1C1C] text-lg tracking-wide"><?php echo e($order->order_number); ?></p>
        <p class="text-xs text-gray-500 mt-1"><?php echo e($order->branch->name ?? '—'); ?> · <?php echo e($order->created_at->format('H:i d/m/Y')); ?></p>
      </div>
      <?php
        $statusConfig = [
          'pending'   => ['bg-yellow-100 text-yellow-700 border-yellow-300', '⏳ Chờ xác nhận'],
          'confirmed' => ['bg-blue-100 text-blue-700 border-blue-300',       '✅ Đã xác nhận'],
          'preparing' => ['bg-orange-100 text-orange-700 border-orange-300', '👨‍🍳 Đang chuẩn bị'],
          'ready'     => ['bg-green-100 text-green-700 border-green-300',    '🎉 Sẵn sàng'],
          'completed' => ['bg-gray-100 text-gray-700 border-gray-300',       '✔️ Hoàn thành'],
          'cancelled' => ['bg-red-100 text-red-700 border-red-300',          '❌ Đã huỷ'],
        ];
        [$cls, $lbl] = $statusConfig[$order->status] ?? ['bg-gray-100 text-gray-600 border-gray-200', $order->status];
      ?>
      <span class="px-3 py-1.5 rounded-xl border-2 text-xs font-black <?php echo e($cls); ?>"><?php echo e($lbl); ?></span>
    </div>
  </div>

  
  <?php if($order->status !== 'cancelled'): ?>
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5">
    <p class="font-black text-[#1C1C1C] text-sm mb-5">Trạng thái đơn hàng</p>
    <div class="relative">
      
      <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
      <div class="space-y-6">
        <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $done    = $currentIdx !== false && $i <= $currentIdx;
          $current = $currentIdx !== false && $i === $currentIdx;
        ?>
        <div class="flex items-start gap-4 relative">
          <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center flex-shrink-0 z-10 text-base transition-all
            <?php echo e($done ? 'bg-[#FF6B35] border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]' : 'bg-white border-gray-300'); ?>">
            <?php echo e($step['icon']); ?>

          </div>
          <div class="flex-1 pt-1.5">
            <p class="font-black text-sm <?php echo e($done ? 'text-[#1C1C1C]' : 'text-gray-400'); ?>"><?php echo e($step['label']); ?></p>
            <?php if($step['time']): ?>
            <p class="text-xs text-gray-400 mt-0.5"><?php echo e($step['time']->format('H:i · d/m/Y')); ?></p>
            <?php elseif($current): ?>
            <p class="text-xs text-[#FF6B35] font-bold mt-0.5 animate-pulse">Đang xử lý...</p>
            <?php endif; ?>
          </div>
          <?php if($current): ?>
          <span class="text-xs bg-[#FFD23F] border border-[#1C1C1C] rounded-lg px-2 py-0.5 font-black text-[#1C1C1C]">Hiện tại</span>
          <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

    
    <?php if(in_array($order->status, ['confirmed','preparing'])): ?>
    <div class="mt-5 bg-orange-50 border-2 border-[#FF6B35] rounded-xl p-3 flex items-center gap-3">
      <span class="text-2xl">⏱️</span>
      <div>
        <p class="font-black text-[#1C1C1C] text-sm">Dự kiến <?php echo e($order->delivery_mode === 'delivery' ? 'giao hàng' : 'sẵn sàng'); ?></p>
        <p class="text-xs text-gray-500">~<?php echo e($order->estimated_eta ?? '20-25'); ?> phút kể từ khi xác nhận</p>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  
  <?php if($order->shipper && $order->status === 'ready'): ?>
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 flex items-center gap-4">
    <div class="w-12 h-12 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center text-white text-xl">🛵</div>
    <div class="flex-1">
      <p class="font-black text-[#1C1C1C] text-sm"><?php echo e($order->shipper->name); ?></p>
      <p class="text-xs text-gray-500">Shipper · <?php echo e($order->shipper->phone); ?></p>
    </div>
    <a href="tel:<?php echo e($order->shipper->phone); ?>" class="bg-green-500 text-white text-xs font-black px-3 py-2 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">📞 Gọi</a>
  </div>
  <?php endif; ?>

  
  <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5">
    <p class="font-black text-[#1C1C1C] text-sm mb-4">Chi tiết đơn (<?php echo e($order->items->count()); ?> món)</p>
    <div class="space-y-3">
      <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="flex items-center gap-3">
        <?php if($item->product?->image): ?>
        <img src="<?php echo e($item->product->image); ?>" alt="<?php echo e($item->product->name); ?>" class="w-12 h-12 object-cover rounded-xl border-2 border-gray-100 flex-shrink-0" />
        <?php else: ?>
        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-xl flex-shrink-0">🍽️</div>
        <?php endif; ?>
        <div class="flex-1">
          <p class="font-bold text-[#1C1C1C] text-sm"><?php echo e($item->product->name ?? 'Sản phẩm'); ?></p>
          <?php if($item->note): ?><p class="text-xs text-orange-500"><?php echo e($item->note); ?></p><?php endif; ?>
        </div>
        <div class="text-right">
          <p class="font-black text-[#1C1C1C] text-sm">x<?php echo e($item->quantity); ?></p>
          <p class="text-xs text-gray-500"><?php echo e(number_format($item->price * $item->quantity)); ?>đ</p>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="border-t-2 border-dashed border-gray-200 mt-4 pt-4 space-y-1.5 text-sm">
      <div class="flex justify-between text-gray-500">
        <span>Tạm tính</span><span><?php echo e(number_format($order->subtotal)); ?>đ</span>
      </div>
      <?php if($order->shipping_fee > 0): ?>
      <div class="flex justify-between text-gray-500">
        <span>Phí vận chuyển</span><span><?php echo e(number_format($order->shipping_fee)); ?>đ</span>
      </div>
      <?php endif; ?>
      <?php if($order->discount_amount > 0): ?>
      <div class="flex justify-between text-green-600">
        <span>Giảm giá</span><span>-<?php echo e(number_format($order->discount_amount)); ?>đ</span>
      </div>
      <?php endif; ?>
      <div class="flex justify-between font-black text-[#1C1C1C] text-base pt-1 border-t border-gray-100">
        <span>Tổng cộng</span><span class="text-[#FF6B35]"><?php echo e(number_format($order->grand_total)); ?>đ</span>
      </div>
      <div class="flex justify-between text-xs text-gray-400 pt-1">
        <span>Thanh toán</span>
        <span><?php echo e(['momo'=>'💜 MoMo','cod'=>'💵 Tiền mặt','zalopay'=>'🔵 ZaloPay','bank'=>'🏦 Chuyển khoản'][$order->payment_method] ?? $order->payment_method); ?></span>
      </div>
    </div>
  </div>

  
  <div class="flex gap-3">
    <a href="<?php echo e(route('client.profile')); ?>" class="flex-1 text-center py-3 rounded-xl border-2 border-[#1C1C1C] bg-white font-black text-sm shadow-[3px_3px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
      ← Lịch sử đơn
    </a>
    <?php if($order->status === 'completed'): ?>
    <a href="<?php echo e(route('client.menu')); ?>" class="flex-1 text-center py-3 rounded-xl border-2 border-[#1C1C1C] bg-[#FF6B35] text-white font-black text-sm shadow-[3px_3px_0px_#1C1C1C] hover:shadow-none hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
      🔄 Đặt lại
    </a>
    <?php endif; ?>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ecom-food\resources\views/client/orders/detail.blade.php ENDPATH**/ ?>