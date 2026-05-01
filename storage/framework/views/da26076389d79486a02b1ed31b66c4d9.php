<?php $__env->startSection('title', 'Đặt đơn nhóm'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#FAFAF8] flex flex-col items-center justify-center p-4">
  <div class="w-full max-w-sm">

    
    <div class="text-center mb-6">
      <div class="w-20 h-20 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] flex items-center justify-center mx-auto mb-3 text-4xl">👥</div>
      <h1 class="text-2xl font-black text-[#1C1C1C]">Đặt Đơn Nhóm</h1>
      <p class="text-sm text-gray-500 mt-1">Tạo phòng, mọi người tự chọn, chia bill tự động! 🎉</p>
    </div>

    
    <form action="<?php echo e(route('client.group-order.create')); ?>" method="POST" class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5 space-y-4">
      <?php echo csrf_field(); ?>
      <div>
        <label class="text-xs font-black text-[#1C1C1C] mb-1 block uppercase tracking-wide">Chi nhánh</label>
        <select name="branch_id" required
          class="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-[#FF6B35] transition-all">
          <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <button type="submit" class="w-full bg-[#FF6B35] text-white font-black py-3.5 rounded-xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-[2px_2px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all flex items-center justify-center gap-2 text-base">
        ⚡ Tạo phòng ngay!
      </button>
    </form>

    
    <div class="mt-4 text-center">
      <p class="text-sm text-gray-500">Đã có mã phòng?</p>
      <form action="<?php echo e(route('client.group-order.join')); ?>" method="GET" class="mt-2 flex gap-2">
        <input type="text" name="code" placeholder="Nhập mã phòng..." class="flex-1 border-2 border-[#1C1C1C] rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35]" />
        <button type="submit" class="bg-[#1C1C1C] text-white font-black px-4 py-2 rounded-xl border-2 border-[#1C1C1C] text-sm">Vào →</button>
      </form>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function copyLink() {
  const code = document.getElementById('room-code').textContent.trim();
  navigator.clipboard.writeText(window.location.origin + '/group-order/' + code);
  const btn = document.getElementById('copy-btn');
  btn.textContent = '✅ Đã copy!';
  setTimeout(() => btn.textContent = '📋 Copy link', 2000);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ecom-food\resources\views/client/group-orders/index.blade.php ENDPATH**/ ?>