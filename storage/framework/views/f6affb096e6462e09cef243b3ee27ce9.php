<?php $__env->startSection('title', 'Super Admin'); ?>
<?php $__env->startSection('page_title', 'Super Admin'); ?>

<?php $__env->startSection('content'); ?>
    <div class="h-full flex flex-col bg-[#0F0F0F]">

        
        <div class="flex border-b-2 border-[#333] bg-[#1A1A1A] flex-shrink-0 overflow-x-auto">
            <?php
                $tabs = [
                    ['id' => 'analytics', 'label' => 'Analytics', 'icon' => '📊', 'perm' => 'view_revenue'],
                    ['id' => 'campaigns', 'label' => 'Campaigns', 'icon' => '🏷️', 'perm' => 'send_push'],
                    ['id' => 'roles', 'label' => 'RBAC', 'icon' => '🔒', 'perm' => 'manage_permissions'],
                    ['id' => 'audit', 'label' => 'Audit', 'icon' => '📋', 'perm' => 'view_audit_log'],
                ];
            ?>
            <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(auth()->user()->hasPermission($tab['perm'])): ?>
                    <a href="<?php echo e(route('admin.super', ['tab' => $tab['id']])); ?>"
                        class="flex items-center gap-1.5 px-4 lg:px-5 py-3 text-xs lg:text-sm font-black border-r border-[#333] transition-all whitespace-nowrap flex-shrink-0
             <?php echo e(($activeTab ?? 'analytics') === $tab['id'] ? 'bg-[#FF6B35] text-white' : 'text-gray-400 hover:text-white'); ?>">
                        <?php echo e($tab['icon']); ?> <?php echo e($tab['label']); ?>

                    </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="flex-1 overflow-y-auto p-4 lg:p-6">

            
            <?php if(($activeTab ?? 'analytics') === 'analytics' && auth()->user()->hasPermission('view_revenue')): ?>
                <div class="space-y-4 lg:space-y-6">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                        <?php $__currentLoopData = [['label' => 'Tổng doanh thu', 'value' => number_format($totalRevenue ?? 0) . 'đ', 'icon' => '💰', 'color' => 'text-green-400', 'bg' => 'border-green-700/30'], ['label' => 'Đơn hôm nay', 'value' => \App\Models\Order\Order::whereDate('created_at', today())->count() . ' đơn', 'icon' => '⚡', 'color' => 'text-orange-400', 'bg' => 'border-orange-700/30'], ['label' => 'Người dùng', 'value' => \App\Models\User\User::where('role', 'customer')->count() . ' người', 'icon' => '👥', 'color' => 'text-blue-400', 'bg' => 'border-blue-700/30'], ['label' => 'Chi nhánh', 'value' => \App\Models\System\Branch::where('status', 'open')->count() . ' mở', 'icon' => '🏪', 'color' => 'text-purple-400', 'bg' => 'border-purple-700/30']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-[#1A1A1A] border-2 <?php echo e($kpi['bg']); ?> rounded-2xl p-3 lg:p-4">
                                <div class="text-xl lg:text-2xl mb-2"><?php echo e($kpi['icon']); ?></div>
                                <div class="text-white font-black text-lg lg:text-2xl"><?php echo e($kpi['value']); ?></div>
                                <div class="text-gray-500 text-xs mt-0.5"><?php echo e($kpi['label']); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                        
                        <div class="lg:col-span-2 bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
                            <h3 class="text-white font-black mb-4 text-sm lg:text-base">Doanh thu 7 ngày qua</h3>
                            <div class="h-40 lg:h-52 flex items-end gap-2 px-2">
                                <?php $__empty_1 = true; $__currentLoopData = $revenueData ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php $h = min(100, (($d['revenue'] ?? 0) / max(1, 25000000)) * 100); ?>
                                    <div class="flex-1 flex flex-col items-center gap-1">
                                        <div class="w-full bg-gradient-to-t from-[#FF6B35] to-[#FFD23F] rounded-t-sm hover:opacity-80 transition-opacity cursor-pointer"
                                            style="height: <?php echo e($h); ?>%"
                                            title="<?php echo e($d['day']); ?>: <?php echo e(number_format($d['revenue'])); ?>đ"></div>
                                        <span class="text-[10px] text-gray-500"><?php echo e($d['day']); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="w-full flex items-center justify-center text-gray-600 text-xs">Chưa có dữ
                                        liệu</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
                            <h3 class="text-white font-black mb-4 text-sm">So sánh chi nhánh</h3>
                            <div class="space-y-3">
                                <?php $__currentLoopData = $branches ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $branchRevenue = \App\Models\Order\Order::where('branch_id',$branch->id)->where('status','completed')->sum('grand_total'); ?>
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <span
                                                class="text-gray-300 text-xs"><?php echo e(str_replace('Chi nhánh ', '', $branch->name)); ?></span>
                                            <span
                                                class="text-white font-black text-xs"><?php echo e(number_format($branchRevenue / 1000000, 1)); ?>M</span>
                                        </div>
                                        <div class="h-3 bg-[#333] rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full"
                                                style="width: <?php echo e(min(100, ($branchRevenue / max(1, 16000000)) * 100)); ?>%">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if(($activeTab ?? '') === 'campaigns' && auth()->user()->hasPermission('send_push')): ?>
                <div class="space-y-4 lg:space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-white font-black text-lg lg:text-xl">Quản lý Campaigns</h2>
                    </div>

                    
                    <?php if(auth()->user()->hasPermission('manage_vouchers')): ?>
                        <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[600px]">
                                    <thead class="bg-[#222] border-b border-[#333]">
                                        <tr>
                                            <?php $__currentLoopData = ['Mã', 'Giảm giá', 'Loại', 'Đã dùng', 'Hạn sử dụng', 'Trạng thái']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <th
                                                    class="text-left text-gray-400 text-xs font-black px-4 py-3 uppercase tracking-wide">
                                                    <?php echo e($h); ?></th>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $vouchers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr class="border-b border-[#222] hover:bg-[#1D1D1D]">
                                                <td class="px-4 py-3 text-white font-black text-sm"><?php echo e($v->code); ?>

                                                </td>
                                                <td class="px-4 py-3 text-[#FFD23F] font-black">
                                                    <?php echo e($v->type === 'flat' ? number_format($v->value) . 'đ' : ($v->type === 'percent' ? $v->value . '%' : 'Free ship')); ?>

                                                </td>
                                                <td class="px-4 py-3 text-gray-400 text-sm">
                                                    <?php echo e($v->type === 'flat' ? 'Cố định' : ($v->type === 'percent' ? 'Phần trăm' : 'Free ship')); ?>

                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-white text-sm font-bold">
                                                        <?php echo e($v->used_count); ?>/<?php echo e($v->max_uses ?? '∞'); ?></div>
                                                    <?php if($v->max_uses): ?>
                                                        <div class="h-1.5 bg-[#333] rounded-full mt-1 w-20">
                                                            <div class="h-full bg-[#FF6B35] rounded-full"
                                                                style="width: <?php echo e(($v->used_count / max(1, $v->max_uses)) * 100); ?>%">
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-4 py-3 text-gray-400 text-sm">
                                                    <?php echo e($v->expires_at ? $v->expires_at->format('d/m/Y') : '∞'); ?></td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="text-xs font-black px-2 py-1 rounded-full <?php echo e($v->isValid() ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-gray-700 text-gray-500'); ?>">
                                                        <?php echo e($v->isValid() ? 'Hoạt động' : 'Hết hạn'); ?>

                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-gray-500 py-6 text-sm">Chưa có
                                                    voucher nào</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4 lg:p-5">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[#FFD23F]">🔔</span>
                            <h3 class="text-white font-black text-sm lg:text-base">Gửi Push Notification</h3>
                        </div>
                        <form action="<?php echo e(route('admin.super.push')); ?>" method="POST" class="space-y-3">
                            <?php echo csrf_field(); ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="text-gray-400 text-xs mb-1 block">Tiêu đề</label>
                                    <input name="title" placeholder="🔥 Flash Sale giờ trưa!"
                                        class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35]" />
                                </div>
                                <div>
                                    <label class="text-gray-400 text-xs mb-1 block">Target segment</label>
                                    <select name="segment"
                                        class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-3 py-2 text-sm outline-none">
                                        <option value="all">Tất cả người dùng</option>
                                        <option value="abandoned_cart">Giỏ hàng bị bỏ quên</option>
                                        <option value="inactive_7d">Chưa đặt trong 7 ngày</option>
                                        <option value="vip">Khách VIP</option>
                                    </select>
                                </div>
                            </div>
                            <textarea name="body" placeholder="Nội dung thông báo..."
                                class="w-full bg-[#222] border border-[#444] text-white rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35] h-20 resize-none"></textarea>
                            <button type="submit"
                                class="bg-[#FF6B35] text-white font-black px-5 py-2.5 rounded-xl border-2 border-[#FF6B35] flex items-center gap-2 text-sm">🔔
                                Gửi ngay</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if(($activeTab ?? '') === 'roles' && auth()->user()->hasPermission('manage_permissions')): ?>
                <div class="space-y-4">
                    <h2 class="text-white font-black text-lg lg:text-xl flex items-center gap-2">🔒 RBAC Permission Matrix
                    </h2>
                    <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[700px]">
                                <thead>
                                    <tr class="bg-[#222] border-b border-[#333]">
                                        <th
                                            class="text-left text-gray-400 text-xs px-4 py-3 font-black uppercase tracking-wide">
                                            Quyền hạn</th>
                                        <?php $__currentLoopData = $roles ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <th class="text-center text-gray-300 text-xs px-3 py-3 font-black">
                                                <?php echo e($role); ?></th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $permissions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="border-b border-[#1D1D1D] hover:bg-[#1D1D1D]">
                                            <td class="px-4 py-3 text-gray-300 text-sm"><?php echo e($perm['label']); ?></td>
                                            <?php $__currentLoopData = $roles ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <td class="text-center px-3 py-3">
                                                    <label
                                                        class="cursor-pointer inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-[#333] transition-colors">
                                                        <input type="checkbox"
                                                            class="perm-toggle w-5 h-5 accent-[#FF6B35] cursor-pointer"
                                                            data-role="<?php echo e($role); ?>"
                                                            data-perm="<?php echo e($perm['key']); ?>"
                                                            <?php echo e(in_array($perm['key'], $rolePerms[$role] ?? []) ? 'checked' : ''); ?>

                                                            <?php echo e($role === 'super_admin' ? 'disabled' : ''); ?> />
                                                    </label>
                                                </td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <script>
                    document.querySelectorAll('.perm-toggle').forEach(cb => {
                        cb.addEventListener('change', async function() {
                            const role = this.dataset.role;
                            const perm = this.dataset.perm;
                            const allowed = this.checked ? 1 : 0;
                            try {
                                const res = await fetch('<?php echo e(route('admin.super.perm')); ?>', {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                                    },
                                    body: JSON.stringify({
                                        role,
                                        permission: perm,
                                        allowed
                                    })
                                });
                                const data = await res.json();
                                if (!data.ok) throw new Error('Failed');
                            } catch (e) {
                                this.checked = !this.checked;
                                alert('Cập nhật thất bại, vui lòng thử lại.');
                            }
                        });
                    });
                </script>
            <?php endif; ?>

            
            <?php if(($activeTab ?? '') === 'audit' && auth()->user()->hasPermission('view_audit_log')): ?>
                <div class="space-y-4">
                    <h2 class="text-white font-black text-lg lg:text-xl flex items-center gap-2">📋 Audit Trail</h2>
                    <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[700px]">
                                <thead>
                                    <tr class="bg-[#222] border-b border-[#333]">
                                        <?php $__currentLoopData = ['Thời gian', 'Người dùng', 'Hành động', 'Bảng', 'Row ID', 'IP']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <th
                                                class="text-left text-gray-500 text-[10px] px-4 py-2.5 font-black uppercase tracking-wide">
                                                <?php echo e($h); ?></th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $auditLogs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="border-b border-[#1D1D1D] hover:bg-[#1D1D1D] font-mono text-xs">
                                            <td class="px-4 py-3 text-gray-500"><?php echo e($log->created_at->format('H:i:s')); ?>

                                            </td>
                                            <td class="px-4 py-3 text-blue-400"><?php echo e($log->user?->email ?? 'system'); ?></td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="font-black px-2 py-0.5 rounded text-[10px] <?php echo e($log->action === 'CREATE' ? 'bg-green-900/50 text-green-400' : ($log->action === 'UPDATE' ? 'bg-yellow-900/50 text-yellow-400' : 'bg-red-900/50 text-red-400')); ?>"><?php echo e($log->action); ?></span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-300"><?php echo e($log->table_name); ?></td>
                                            <td class="px-4 py-3 text-gray-400">#<?php echo e($log->row_id); ?></td>
                                            <td class="px-4 py-3 text-gray-600"><?php echo e($log->ip_address); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-gray-500 py-6 text-sm">Chưa có log
                                                nào</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ecom-food\resources\views/admin/dashboard/super.blade.php ENDPATH**/ ?>