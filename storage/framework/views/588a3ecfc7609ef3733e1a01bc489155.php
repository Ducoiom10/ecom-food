<?php $__env->startSection('title', 'Giỏ hàng'); ?>

<?php $__env->startSection('content'); ?>
    <div class="p-4 lg:p-8 max-w-7xl mx-auto">

        <?php if(session('success')): ?>
            <div
                class="bg-green-50 border-2 border-green-400 rounded-xl px-4 py-3 mb-4 text-green-700 font-bold flex items-center gap-2">
                <span>✅</span> <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div
                class="bg-red-50 border-2 border-red-400 rounded-xl px-4 py-3 mb-4 text-red-700 font-bold flex items-center gap-2">
                <span>⚠️</span> <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <h1 class="font-black text-[#1C1C1C] text-2xl lg:text-3xl mb-6">Giỏ hàng của bạn 🛒</h1>

        
        <?php
            $freeShipTarget = 100000;
            $remaining = max(0, $freeShipTarget - $subtotal);
            $pct = min(100, ($subtotal / $freeShipTarget) * 100);
        ?>
        <?php if($remaining > 0): ?>
            <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">🚵</span>
                    <p class="text-sm font-bold text-[#1C1C1C]">
                        Mua thêm <span class="text-[#FF6B35] font-black text-lg"><?php echo e(number_format($remaining)); ?>đ</span> để
                        được <span class="text-green-600 font-black text-lg">MIỄN PHÍ GIAO HÀNG!</span>
                    </p>
                </div>
                <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                    <div class="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full transition-all"
                        style="width: <?php echo e($pct); ?>%"></div>
                    <span class="absolute text-lg -translate-x-1/2 -translate-y-1/2"
                        style="left: <?php echo e($pct); ?>%; top: 50%;">🚵</span>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-green-50 border-2 border-green-300 rounded-2xl p-4 mb-6 flex items-center gap-2">
                <span class="text-2xl">🎉</span>
                <p class="text-sm font-bold text-green-700">Bạn được <span class="font-black text-lg">MIỄN PHÍ GIAO
                        HÀNG</span> cho đơn này!</p>
            </div>
        <?php endif; ?>

        <?php if(!empty($cart)): ?>
            <div class="space-y-4 mb-8">
                <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden flex items-center p-4 lg:p-6"
                        id="cart-item-<?php echo e($item['id']); ?>">
                        <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>"
                            class="w-20 lg:w-28 h-20 lg:h-28 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
                        <div class="flex-1 pl-4 space-y-2">
                            <div class="font-black text-[#1C1C1C] text-lg leading-tight"><?php echo e($item['name']); ?></div>
                            <?php if(!empty($item['option_labels'])): ?>
                                <div class="text-sm text-gray-500">+ <?php echo e(implode(', ', $item['option_labels'])); ?></div>
                            <?php endif; ?>
                            <?php if(!empty($item['note'])): ?>
                                <div class="text-sm text-orange-500 font-medium">Ghi chú: <?php echo e($item['note']); ?></div>
                            <?php endif; ?>
                            <div class="flex items-center justify-between pt-2">
                                <span class="font-black text-[#FF6B35] text-xl" id="item-price-<?php echo e($item['id']); ?>"
                                    data-price="<?php echo e($item['price']); ?>"><?php echo e(number_format($item['price'] * $item['quantity'])); ?>đ</span>
                                <div class="flex items-center gap-3">
                                    <button onclick="updateQty('<?php echo e($item['id']); ?>', -1, <?php echo e($item['price']); ?>)"
                                        class="w-12 h-12 rounded-xl border-2 border-[#1C1C1C] bg-white flex items-center justify-center font-black text-xl shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none">−</button>
                                    <span class="font-black text-[#1C1C1C] text-2xl w-12 text-center min-w-[3rem]"
                                        id="qty-<?php echo e($item['id']); ?>"><?php echo e($item['quantity']); ?></span>
                                    <button onclick="updateQty('<?php echo e($item['id']); ?>', 1, <?php echo e($item['price']); ?>)"
                                        class="w-12 h-12 rounded-xl border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center font-black text-xl shadow-[2px_2px_0px_#1C1C1C] hover:shadow-none">+</button>
                                    <form method="POST" action="<?php echo e(route('client.cart.remove', $item['id'])); ?>"
                                        class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" onclick="return confirm('Xóa <?php echo e($item['name']); ?>?')"
                                            class="w-12 h-12 rounded-xl border-2 border-red-200 bg-red-50 text-red-500 flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C] hover:shadow-none transition-all">
                                            <span class="text-lg">🗑</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <?php if($upsell): ?>
                <div
                    class="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-6 flex items-center gap-4 mb-8">
                    <img src="<?php echo e($upsell->image); ?>" alt="<?php echo e($upsell->name); ?>"
                        class="w-20 h-20 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
                    <div class="flex-1">
                        <h3 class="font-black text-[#1C1C1C] text-lg mb-1">Thêm <?php echo e($upsell->name); ?>?</h3>
                        <p class="text-[#1C1C1C]/70 text-sm mb-3">Chỉ <?php echo e(number_format($upsell->base_price)); ?>đ - Perfect
                            combo!</p>
                        <div class="flex gap-3">
                            <button onclick="addUpsell(<?php echo e($upsell->id); ?>)"
                                class="bg-[#FF6B35] text-white font-black px-6 py-2.5 rounded-xl border border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]">+
                                Thêm ngay</button>
                            <button onclick="this.parentElement.parentElement.parentElement.remove()"
                                class="text-[#1C1C1C] font-bold underline hover:no-underline">Bỏ qua</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-6 lg:p-8">
                <h3 class="font-black text-[#1C1C1C] text-xl mb-4 flex items-center gap-2">Tóm tắt <span
                        class="text-2xl">(<?php echo e(count($cart)); ?> món)</span></h3>
                <div class="space-y-3 mb-6 text-lg">
                    <div class="flex justify-between font-black">
                        <span>Tạm tính:</span>
                        <span class="text-[#FF6B35]"><?php echo e(number_format($subtotal)); ?>đ</span>
                    </div>
                    <?php if($remaining > 0): ?>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Còn lại để freeship:</span>
                            <span class="font-black text-green-600"><?php echo e(number_format($remaining)); ?>đ</span>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Checkout moved to full form below -->
                <p class="text-center text-xs text-gray-400 mt-3">Bạn có thể chỉnh sửa giỏ hàng trước khi thanh toán</p>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-white border-2 border-dashed border-gray-200 rounded-2xl">
                <div class="text-6xl mb-6">🛒</div>
                <h2 class="font-black text-2xl text-[#1C1C1C] mb-3">Giỏ hàng trống</h2>
                <p class="text-gray-500 text-lg mb-8">Chưa có món nào trong giỏ hàng của bạn</p>
                <a href="<?php echo e(route('client.menu')); ?>"
                    class="bg-[#FF6B35] text-white font-black px-8 py-4 rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] text-lg hover:shadow-none transition-all">🍜
                    Chọn món ngay</a>
            </div>
        <?php endif; ?>

    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function updateQty(id, delta, price) {
                const qtyEl = document.getElementById('qty-' + id);
                const priceEl = document.getElementById('item-price-' + id);
                let qty = Math.max(1, parseInt(qtyEl.textContent) + delta);
                qtyEl.textContent = qty;
                priceEl.textContent = (price * qty).toLocaleString('vi-VN') + 'đ';

                fetch(`/cart/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({
                        quantity: qty
                    })
                });
            }

            function addUpsell(id) {
                fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({
                        product_id: id,
                        quantity: 1
                    })
                }).then(r => r.json()).then(() => {
                    location.reload();
                });
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ecom-food\resources\views/client/cart/summary.blade.php ENDPATH**/ ?>