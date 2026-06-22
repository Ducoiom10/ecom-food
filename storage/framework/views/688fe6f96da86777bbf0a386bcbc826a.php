<?php $__env->startSection('title', 'Bếp KDS'); ?>
<?php $__env->startSection('page_title', 'Bếp KDS'); ?>

<?php $__env->startSection('content'); ?>
    <div class="flex flex-col h-full bg-[#0F0F0F]">

        
        <div id="offline-banner"
            class="hidden bg-red-600 border-b-2 border-red-400 px-6 py-3 flex items-center gap-3 z-50 flex-shrink-0">
            <span class="text-white text-lg flex-shrink-0">📡</span>
            <div class="flex-1">
                <p class="text-white font-black text-sm">Mất kết nối mạng!</p>
                <p class="text-red-200 text-xs">Đang hoạt động ở chế độ Offline · Hành động sẽ được sync khi có mạng trở lại
                </p>
            </div>
            <div class="bg-red-700 text-white text-xs font-black px-3 py-1.5 rounded-lg border border-red-400 animate-pulse">
                OFFLINE MODE</div>
        </div>

        
        <div class="bg-[#1A1A1A] border-b-2 border-[#333] px-4 lg:px-6 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5" id="net-status">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-green-400 text-xs font-bold">Online</span>
                </div>
                <button onclick="toggleOfflineSim()" id="offline-sim-btn"
                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-600 text-gray-400 hover:border-red-500 hover:text-red-400 font-bold transition-all">
                    Mô phỏng Offline
                </button>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex border border-[#444] rounded-xl overflow-hidden">
                    <button onclick="switchView('kanban')" id="view-kanban"
                        class="px-3 py-1.5 text-xs font-bold bg-[#FF6B35] text-white transition-all">👨‍🍳 Kanban</button>
                    <button onclick="switchView('inventory')" id="view-inventory"
                        class="px-3 py-1.5 text-xs font-bold text-gray-400 hover:text-white transition-all">📦 Kho</button>
                </div>
                <div class="text-gray-400 text-xs font-mono" id="kds-clock"><?php echo e(now()->format('H:i')); ?></div>
            </div>
        </div>

        
        <div id="view-kanban-panel" class="flex-1 overflow-hidden">
            <div class="h-full flex flex-col lg:flex-row overflow-auto lg:overflow-hidden">

                <?php
                    $columns = [
                        [
                            'statuses' => ['pending'],
                            'title' => 'Chờ Duyệt',
                            'color' => 'bg-orange-400',
                            'action' => 'Duyệt đơn ✓',
                            'actionColor' => 'bg-orange-500 hover:bg-orange-600 text-white',
                            'route' => 'admin.kds.confirm',
                        ],
                        [
                            'statuses' => ['confirmed'],
                            'title' => 'Cần Làm',
                            'color' => 'bg-green-500',
                            'action' => 'Bắt đầu nấu',
                            'actionColor' => 'bg-green-500 hover:bg-green-600 text-white',
                            'route' => 'admin.kds.move',
                        ],
                        [
                            'statuses' => ['preparing'],
                            'title' => 'Đang Nấu',
                            'color' => 'bg-yellow-500',
                            'action' => 'Xong! Giao bàn',
                            'actionColor' => 'bg-yellow-500 hover:bg-yellow-600 text-black',
                            'route' => 'admin.kds.move',
                        ],
                        [
                            'statuses' => ['ready'],
                            'title' => 'Sẵn Sàng',
                            'color' => 'bg-blue-500',
                            'action' => 'Đã giao ✓',
                            'actionColor' => 'bg-blue-500 hover:bg-blue-600 text-white',
                            'route' => 'admin.kds.move',
                        ],
                    ];
                ?>

                <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $colOrders = collect($orders)->whereIn('status', $col['statuses']); ?>
                    <div
                        class="flex-1 flex flex-col border-b-2 lg:border-b-0 lg:border-r border-[#222] min-w-0 min-h-[300px] lg:min-h-0">
                        <div
                            class="px-4 py-3 border-b-2 border-[#333] flex items-center justify-between flex-shrink-0 bg-[#1A1A1A]">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full <?php echo e($col['color']); ?>"></div>
                                <span
                                    class="text-white font-black text-sm uppercase tracking-wide"><?php echo e($col['title']); ?></span>
                            </div>
                            <span
                                class="<?php echo e($col['color']); ?> text-white text-xs font-black w-6 h-6 rounded-full flex items-center justify-center"><?php echo e($colOrders->count()); ?></span>
                        </div>

                        <div class="flex-1 overflow-y-auto p-3 space-y-3">
                            <?php $__empty_1 = true; $__currentLoopData = $colOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div
                                    class="bg-[#1A1A1A] border-2 rounded-2xl overflow-hidden transition-all
            <?php echo e($order->priority === 'high' ? 'border-[#FF6B35] shadow-[4px_4px_0px_#FF6B35]' : 'border-[#333] shadow-[4px_4px_0px_#333]'); ?>">

                                    <div class="px-4 py-3 border-b border-[#333] flex items-center justify-between">
                                        <div>
                                            <div class="text-white font-black text-base"><?php echo e($order->order_number); ?></div>
                                            <div class="text-gray-400 text-xs">
                                                <?php echo e($order->delivery_mode === 'delivery' ? '🛵 Giao hàng' : '🏪 Tự lấy'); ?>

                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <?php if($order->status !== 'pending'): ?>
                                                <div class="font-black text-xl <?php echo e($order->elapsed_minutes < 10 ? 'text-green-400' : ($order->elapsed_minutes < 20 ? 'text-yellow-400' : 'text-red-400')); ?>"
                                                    data-confirmed-at="<?php echo e(($order->confirmed_at ?? $order->created_at)->toISOString()); ?>">
                                                    <?php echo e($order->elapsed_minutes); ?>m
                                                </div>
                                            <?php endif; ?>
                                            <?php if($order->priority === 'high'): ?>
                                                <div class="text-[#FF6B35] text-[10px] font-black">⚡ ƯU TIÊN</div>
                                            <?php endif; ?>
                                            <?php if($order->status !== 'pending' && $order->elapsed_minutes > 20): ?>
                                                <div class="text-red-400 text-[10px] font-black">⚠️ TRỄ!</div>
                                            <?php endif; ?>
                                            <?php if($order->status === 'pending'): ?>
                                                <div class="text-orange-400 text-[10px] font-black">⏳ CHỜ DUYỆT</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="px-4 py-3 space-y-2">
                                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex gap-3">
                                                <div
                                                    class="w-9 h-9 bg-[#FF6B35] rounded-xl flex items-center justify-center text-white font-black text-lg flex-shrink-0">
                                                    <?php echo e($item->quantity); ?></div>
                                                <div class="flex-1">
                                                    <div class="text-white font-black text-sm leading-tight">
                                                        <?php echo e($item->product->name); ?></div>
                                                    <?php if($item->options->isNotEmpty()): ?>
                                                        <div class="text-[#FFD23F] text-xs mt-0.5">+
                                                            <?php echo e($item->options->map(fn($o) => $o->optionValue->label)->implode(', ')); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if($item->note): ?>
                                                        <div class="text-orange-400 text-xs font-bold mt-0.5">⚠️
                                                            <?php echo e($item->note); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>

                                    <div class="px-4 pb-4">
                                        <form action="<?php echo e(route($col['route'], $order->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit"
                                                class="w-full py-3 rounded-xl font-black text-sm border-2 border-[#444] transition-all <?php echo e($col['actionColor']); ?>">
                                                <?php echo e($col['action']); ?>

                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-center py-12 text-gray-600">
                                    <div class="text-5xl mb-2 opacity-30">👨‍🍳</div>
                                    <p class="text-sm">Trống</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
        </div>

        
        <div id="view-inventory-panel" class="flex-1 overflow-y-auto p-4 lg:p-6 hidden">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-white font-black text-lg mb-4 flex items-center gap-2">📦 Quản lý Nguyên liệu</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php $__empty_1 = true; $__currentLoopData = $inventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div
                            class="bg-[#1A1A1A] border-2 rounded-2xl p-4 transition-all
          <?php echo e($item->current_qty <= 0 ? 'border-red-400' : ($item->isLow() ? 'border-yellow-400' : 'border-[#333]')); ?>">
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-white font-black"><?php echo e($item->name); ?></span>
                                        <?php if($item->current_qty <= 0): ?>
                                            <span
                                                class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full animate-pulse">HẾT
                                                HÀNG</span>
                                        <?php elseif($item->isLow()): ?>
                                            <span
                                                class="bg-yellow-500 text-black text-[10px] font-black px-2 py-0.5 rounded-full">THẤP</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-gray-400 mb-2">Còn: <span
                                            class="text-white font-bold"><?php echo e($item->current_qty); ?>

                                            <?php echo e($item->unit); ?></span> / Ngưỡng: <?php echo e($item->min_threshold); ?></div>
                                    <div class="h-2 bg-[#333] rounded-full overflow-hidden">
                                        <div class="h-full rounded-full <?php echo e($item->current_qty <= 0 ? 'bg-red-500' : ($item->isLow() ? 'bg-yellow-500' : 'bg-green-500')); ?>"
                                            style="width: <?php echo e($item->max_qty > 0 ? min(100, ($item->current_qty / $item->max_qty) * 100) : 0); ?>%">
                                        </div>
                                    </div>
                                </div>
                                <form action="<?php echo e(route('admin.kds.inventory', $item->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <input type="hidden" name="current_qty"
                                        value="<?php echo e($item->current_qty <= 0 ? $item->max_qty : 0); ?>" />
                                    <button type="submit"
                                        class="w-16 h-8 rounded-full border-2 transition-all flex items-center <?php echo e($item->current_qty > 0 ? 'bg-green-500 border-green-500 justify-end' : 'bg-gray-700 border-gray-600 justify-start'); ?>">
                                        <div class="w-7 h-7 bg-white rounded-full shadow-md mx-0.5"></div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-span-2 text-center py-8 text-gray-600">
                            <p class="text-sm">Chưa có dữ liệu kho</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function switchView(view) {
            ['kanban', 'inventory'].forEach(v => {
                document.getElementById('view-' + v + '-panel').classList.toggle('hidden', v !== view);
                const btn = document.getElementById('view-' + v);
                if (v === view) {
                    btn.classList.add('bg-[#FF6B35]', 'text-white');
                    btn.classList.remove('text-gray-400');
                } else {
                    btn.classList.remove('bg-[#FF6B35]', 'text-white');
                    btn.classList.add('text-gray-400');
                }
            });
        }

        // Offline simulation
        let simOffline = false;

        function toggleOfflineSim() {
            simOffline = !simOffline;
            const banner = document.getElementById('offline-banner');
            const btn = document.getElementById('offline-sim-btn');
            const status = document.getElementById('net-status');
            if (simOffline) {
                banner.classList.remove('hidden');
                btn.textContent = 'Kết nối lại';
                status.innerHTML =
                    '<div class="w-2 h-2 bg-red-500 rounded-full"></div><span class="text-red-400 text-xs font-bold">Offline</span>';
            } else {
                banner.classList.add('hidden');
                btn.textContent = 'Mô phỏng Offline';
                status.innerHTML =
                    '<div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div><span class="text-green-400 text-xs font-bold">Online</span>';
            }
        }
        window.addEventListener('offline', () => document.getElementById('offline-banner').classList.remove('hidden'));
        window.addEventListener('online', () => document.getElementById('offline-banner').classList.add('hidden'));

        // Clock
        setInterval(() => {
            document.getElementById('kds-clock').textContent = new Date().toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }, 1000);

        // Auto-refresh elapsed minutes mỗi 30s
        setInterval(() => {
            document.querySelectorAll('[data-confirmed-at]').forEach(el => {
                const confirmedAt = new Date(el.dataset.confirmedAt);
                const elapsed = Math.floor((Date.now() - confirmedAt.getTime()) / 60000);
                el.textContent = elapsed + 'm';
                el.className = el.className.replace(/text-(green|yellow|red)-400( animate-pulse)?/g, '');
                if (elapsed < 10) el.classList.add('text-green-400');
                else if (elapsed < 20) el.classList.add('text-yellow-400');
                else el.classList.add('text-red-400', 'animate-pulse');
            });
        }, 30000);

        // Auto-reload kanban mỗi 60s
        setInterval(() => {
            if (document.getElementById('view-kanban-panel') && !document.getElementById('view-kanban-panel')
                .classList.contains('hidden')) {
                window.location.reload();
            }
        }, 60000);
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ecom-food\resources\views/admin/dashboard/kds.blade.php ENDPATH**/ ?>