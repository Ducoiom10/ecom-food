@extends('layouts.admin')
@section('title', 'Super Admin')
@section('page_title', 'Super Admin')

@section('content')
    <div class="h-full flex flex-col bg-[#0F0F0F]">

        {{-- Tabs --}}
        <div class="flex border-b-2 border-[#333] bg-[#1A1A1A] flex-shrink-0 overflow-x-auto">
            @foreach ([['id' => 'analytics', 'label' => 'Analytics', 'icon' => '📊'], ['id' => 'campaigns', 'label' => 'Campaigns', 'icon' => '🏷️'], ['id' => 'roles', 'label' => 'RBAC', 'icon' => '🔒'], ['id' => 'audit', 'label' => 'Audit', 'icon' => '📋']] as $tab)
                <a href="{{ route('admin.super', ['tab' => $tab['id']]) }}"
                    class="flex items-center gap-1.5 px-4 lg:px-5 py-3 text-xs lg:text-sm font-black border-r border-[#333] transition-all whitespace-nowrap flex-shrink-0
             {{ ($activeTab ?? 'analytics') === $tab['id'] ? 'bg-[#FF6B35] text-white' : 'text-gray-400 hover:text-white' }}">
                    {{ $tab['icon'] }} {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex-1 overflow-y-auto p-4 lg:p-6">

            {{-- ANALYTICS --}}
            @if (($activeTab ?? 'analytics') === 'analytics')
                <div class="space-y-4 lg:space-y-6">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                        @foreach ([['label' => 'Tổng doanh thu', 'value' => number_format($totalRevenue ?? 0) . 'đ', 'icon' => '💰', 'color' => 'text-green-400', 'bg' => 'border-green-700/30'], ['label' => 'Đơn hôm nay', 'value' => \App\Models\Order\Order::whereDate('created_at', today())->count() . ' đơn', 'icon' => '⚡', 'color' => 'text-orange-400', 'bg' => 'border-orange-700/30'], ['label' => 'Người dùng', 'value' => \App\Models\User\User::where('role', 'customer')->count() . ' người', 'icon' => '👥', 'color' => 'text-blue-400', 'bg' => 'border-blue-700/30'], ['label' => 'Chi nhánh', 'value' => \App\Models\System\Branch::where('status', 'open')->count() . ' mở', 'icon' => '🏪', 'color' => 'text-purple-400', 'bg' => 'border-purple-700/30']] as $kpi)
                            <div class="bg-[#1A1A1A] border-2 {{ $kpi['bg'] }} rounded-2xl p-3 lg:p-4">
                                <div class="text-xl lg:text-2xl mb-2">{{ $kpi['icon'] }}</div>
                                <div class="text-white font-black text-lg lg:text-2xl">{{ $kpi['value'] }}</div>
                                <div class="text-gray-500 text-xs mt-0.5">{{ $kpi['label'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                        {{-- Revenue chart --}}
                        <div class="lg:col-span-2 bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
                            <h3 class="text-white font-black mb-4 text-sm lg:text-base">Doanh thu 7 ngày qua</h3>
                            <div class="h-40 lg:h-52 flex items-end gap-2 px-2">
                                @forelse($revenueData ?? [] as $d)
                                    @php $h = min(100, (($d['revenue'] ?? 0) / max(1, 25000000)) * 100); @endphp
                                    <div class="flex-1 flex flex-col items-center gap-1">
                                        <div class="w-full bg-gradient-to-t from-[#FF6B35] to-[#FFD23F] rounded-t-sm hover:opacity-80 transition-opacity cursor-pointer"
                                            style="height: {{ $h }}%"
                                            title="{{ $d['day'] }}: {{ number_format($d['revenue']) }}đ"></div>
                                        <span class="text-[10px] text-gray-500">{{ $d['day'] }}</span>
                                    </div>
                                @empty
                                    <div class="w-full flex items-center justify-center text-gray-600 text-xs">Chưa có dữ
                                        liệu</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Branch comparison --}}
                        <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
                            <h3 class="text-white font-black mb-4 text-sm">So sánh chi nhánh</h3>
                            <div class="space-y-3">
                                @foreach ($branches ?? [] as $branch)
                                    @php $branchRevenue = \App\Models\Order\Order::where('branch_id',$branch->id)->where('status','completed')->sum('grand_total'); @endphp
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <span
                                                class="text-gray-300 text-xs">{{ str_replace('Chi nhánh ', '', $branch->name) }}</span>
                                            <span
                                                class="text-white font-black text-xs">{{ number_format($branchRevenue / 1000000, 1) }}M</span>
                                        </div>
                                        <div class="h-3 bg-[#333] rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full"
                                                style="width: {{ min(100, ($branchRevenue / max(1, 16000000)) * 100) }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- CAMPAIGNS --}}
            @if (($activeTab ?? '') === 'campaigns')
                <div class="space-y-4 lg:space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-white font-black text-lg lg:text-xl">Quản lý Campaigns</h2>
                    </div>

                    {{-- Vouchers table --}}
                    <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[600px]">
                                <thead class="bg-[#222] border-b border-[#333]">
                                    <tr>
                                        @foreach (['Mã', 'Giảm giá', 'Loại', 'Đã dùng', 'Hạn sử dụng', 'Trạng thái'] as $h)
                                            <th
                                                class="text-left text-gray-400 text-xs font-black px-4 py-3 uppercase tracking-wide">
                                                {{ $h }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vouchers ?? [] as $v)
                                        <tr class="border-b border-[#222] hover:bg-[#1D1D1D]">
                                            <td class="px-4 py-3 text-white font-black text-sm">{{ $v->code }}</td>
                                            <td class="px-4 py-3 text-[#FFD23F] font-black">
                                                {{ $v->type === 'flat' ? number_format($v->value) . 'đ' : ($v->type === 'percent' ? $v->value . '%' : 'Free ship') }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-400 text-sm">
                                                {{ $v->type === 'flat' ? 'Cố định' : ($v->type === 'percent' ? 'Phần trăm' : 'Free ship') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-white text-sm font-bold">
                                                    {{ $v->used_count }}/{{ $v->max_uses ?? '∞' }}</div>
                                                @if ($v->max_uses)
                                                    <div class="h-1.5 bg-[#333] rounded-full mt-1 w-20">
                                                        <div class="h-full bg-[#FF6B35] rounded-full"
                                                            style="width: {{ ($v->used_count / max(1, $v->max_uses)) * 100 }}%">
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-gray-400 text-sm">
                                                {{ $v->expires_at ? $v->expires_at->format('d/m/Y') : '∞' }}</td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="text-xs font-black px-2 py-1 rounded-full {{ $v->isValid() ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-gray-700 text-gray-500' }}">
                                                    {{ $v->isValid() ? 'Hoạt động' : 'Hết hạn' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-gray-500 py-6 text-sm">Chưa có
                                                voucher nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Push notification --}}
                    <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4 lg:p-5">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[#FFD23F]">🔔</span>
                            <h3 class="text-white font-black text-sm lg:text-base">Gửi Push Notification</h3>
                        </div>
                        <form action="{{ route('admin.super.push') }}" method="POST" class="space-y-3">
                            @csrf
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
            @endif

            {{-- ROLES --}}
            @if (($activeTab ?? '') === 'roles')
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
                                        @foreach ($roles ?? [] as $role)
                                            <th class="text-center text-gray-300 text-xs px-3 py-3 font-black">
                                                {{ $role }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions ?? [] as $perm)
                                        <tr class="border-b border-[#1D1D1D] hover:bg-[#1D1D1D]">
                                            <td class="px-4 py-3 text-gray-300 text-sm">{{ $perm['label'] }}</td>
                                            @foreach ($roles ?? [] as $role)
                                                <td class="text-center px-3 py-3">
                                                    <label
                                                        class="cursor-pointer inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-[#333] transition-colors">
                                                        <input type="checkbox"
                                                            class="perm-toggle w-5 h-5 accent-[#FF6B35] cursor-pointer"
                                                            data-role="{{ $role }}"
                                                            data-perm="{{ $perm['key'] }}"
                                                            {{ in_array($perm['key'], $rolePerms[$role] ?? []) ? 'checked' : '' }}
                                                            {{ $role === 'super_admin' ? 'disabled' : '' }} />
                                                    </label>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
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
                                const res = await fetch('{{ route('admin.super.perm') }}', {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
            @endif

            {{-- AUDIT --}}
            @if (($activeTab ?? '') === 'audit')
                <div class="space-y-4">
                    <h2 class="text-white font-black text-lg lg:text-xl flex items-center gap-2">📋 Audit Trail</h2>
                    <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[700px]">
                                <thead>
                                    <tr class="bg-[#222] border-b border-[#333]">
                                        @foreach (['Thời gian', 'Người dùng', 'Hành động', 'Bảng', 'Row ID', 'IP'] as $h)
                                            <th
                                                class="text-left text-gray-500 text-[10px] px-4 py-2.5 font-black uppercase tracking-wide">
                                                {{ $h }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($auditLogs ?? [] as $log)
                                        <tr class="border-b border-[#1D1D1D] hover:bg-[#1D1D1D] font-mono text-xs">
                                            <td class="px-4 py-3 text-gray-500">{{ $log->created_at->format('H:i:s') }}
                                            </td>
                                            <td class="px-4 py-3 text-blue-400">{{ $log->user?->email ?? 'system' }}</td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="font-black px-2 py-0.5 rounded text-[10px] {{ $log->action === 'CREATE' ? 'bg-green-900/50 text-green-400' : ($log->action === 'UPDATE' ? 'bg-yellow-900/50 text-yellow-400' : 'bg-red-900/50 text-red-400') }}">{{ $log->action }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-300">{{ $log->table_name }}</td>
                                            <td class="px-4 py-3 text-gray-400">#{{ $log->row_id }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ $log->ip_address }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-gray-500 py-6 text-sm">Chưa có
                                                audit log nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
