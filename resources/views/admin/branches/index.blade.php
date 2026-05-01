@extends('layouts.admin')
@section('title', 'Chi nhánh')
@section('page_title', 'Chi nhánh')

@section('content')
    <div class="h-full overflow-y-auto bg-[#0F0F0F] p-4 lg:p-6">

        {{-- Branch selector --}}
        <div class="flex gap-2 mb-6 flex-wrap">
            @foreach ($branches ?? [] as $branch)
                <a href="{{ route('admin.branch', ['branch' => $branch->id]) }}"
                    class="flex items-center gap-2 px-3 lg:px-4 py-2 rounded-xl border-2 text-xs lg:text-sm font-bold transition-all
             {{ $selectedBranch?->id === $branch->id ? 'bg-[#FF6B35] text-white border-[#FF6B35]' : 'bg-[#1A1A1A] text-gray-400 border-[#333] hover:border-[#FF6B35]/50' }}
             {{ $branch->status === 'closed' ? 'opacity-50 pointer-events-none' : '' }}">
                    <div class="w-2 h-2 rounded-full {{ $branch->status === 'open' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                    <span class="hidden sm:inline">{{ $branch->name }}</span>
                    <span class="sm:hidden">{{ str_replace('Chi nhánh ', '', $branch->name) }}</span>
                    @if ($branch->status === 'closed')
                        <span class="text-[10px]">(Đóng)</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- KPI cards --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 lg:gap-4 mb-6">
            @foreach ([['label' => 'Doanh thu hôm nay', 'value' => number_format($revenue ?? 0) . 'đ', 'change' => '+12%', 'icon' => '💰', 'color' => 'text-green-400'], ['label' => 'Tổng đơn', 'value' => ($orderCount ?? 0) . ' đơn', 'change' => '+8%', 'icon' => '🛍', 'color' => 'text-blue-400'], ['label' => 'Đánh giá TB', 'value' => ($avgRating ?? 0) . ' ⭐', 'change' => '+0.1', 'icon' => '⭐', 'color' => 'text-yellow-400'], ['label' => 'TB đơn/giờ', 'value' => round(($orderCount ?? 0) / max(1, 14)) . ' đơn', 'change' => 'bình thường', 'icon' => '📈', 'color' => 'text-orange-400']] as $kpi)
                <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-3 lg:p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xl lg:text-2xl">{{ $kpi['icon'] }}</span>
                        <span class="text-xs font-bold {{ $kpi['color'] }}">{{ $kpi['change'] }}</span>
                    </div>
                    <div class="text-white font-black text-lg lg:text-xl">{{ $kpi['value'] }}</div>
                    <div class="text-gray-500 text-xs mt-0.5">{{ $kpi['label'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- Main grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6">

            {{-- Revenue chart --}}
            <div class="xl:col-span-2 bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
                <h3 class="text-white font-black mb-4 text-sm lg:text-base">Doanh thu theo giờ —
                    {{ $selectedBranch?->name ?? '' }}</h3>
                <div class="h-40 lg:h-52 flex items-end gap-1 px-2">
                    @forelse($hourlyData ?? [] as $d)
                        @php $h = min(100, (($d['total'] ?? 0) / max(1, 20)) * 100); @endphp
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full bg-[#FF6B35] rounded-t-sm hover:bg-[#FFD23F] transition-colors cursor-pointer"
                                style="height: {{ $h }}%"
                                title="{{ $d['hour'] }}: {{ $d['total'] }} đơn"></div>
                            <span class="text-[7px] lg:text-[9px] text-gray-500">{{ $d['hour'] }}</span>
                        </div>
                    @empty
                        <div class="w-full flex items-center justify-center text-gray-600 text-xs">Chưa có dữ liệu hôm nay
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Right panel --}}
            <div class="space-y-4">

                {{-- Pending orders --}}
                <div class="bg-[#1A1A1A] border-2 border-yellow-500/30 rounded-2xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-yellow-400">⏳</span>
                        <span class="text-white font-black text-sm">Đơn chờ duyệt</span>
                        <span
                            class="bg-yellow-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">{{ $pendingCount ?? 0 }}</span>
                    </div>
                    <div class="space-y-3">
                        @forelse($pendingOrders ?? [] as $po)
                            <div class="rounded-xl p-3 border bg-yellow-900/20 border-yellow-700/30">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-white text-xs font-black">{{ $po->order_number }}</span>
                                    <span class="text-[10px] font-black text-yellow-400">Chờ duyệt</span>
                                </div>
                                <div class="text-gray-400 text-[10px] mb-1">
                                    {{ $po->user?->name }} · {{ $po->items->count() }} món ·
                                    {{ number_format($po->grand_total) }}đ
                                </div>
                                <div class="flex gap-2 mt-2">
                                    <form action="{{ route('admin.branch.confirm', $po->id) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-500 text-white text-[10px] font-black py-1.5 rounded-lg transition-colors">✓
                                            Duyệt</button>
                                    </form>
                                    <form action="{{ route('admin.branch.refund', $po->id) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="reason" value="Từ chối bởi quản lý chi nhánh">
                                        <button type="submit"
                                            class="w-full bg-red-600 hover:bg-red-500 text-white text-[10px] font-black py-1.5 rounded-lg transition-colors">✕
                                            Từ chối</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-xs text-center py-2">Không có đơn chờ duyệt ✓</p>
                        @endforelse
                    </div>
                </div>

                {{-- Inventory alerts --}}
                <div class="bg-[#1A1A1A] border-2 border-red-500/30 rounded-2xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-red-400">⚠️</span>
                        <span class="text-white font-black text-sm">Cảnh báo tồn kho</span>
                        <span
                            class="bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">{{ $lowStock->count() }}</span>
                    </div>
                    <div class="space-y-2">
                        @forelse($lowStock as $item)
                            <div
                                class="flex items-center justify-between text-xs rounded-xl px-3 py-2
            {{ $item->current_qty <= 0 ? 'bg-red-900/30 border border-red-700/30' : 'bg-yellow-900/20 border border-yellow-700/20' }}">
                                <span class="text-white font-bold">{{ $item->name }}</span>
                                <div class="text-right">
                                    <span
                                        class="font-black {{ $item->current_qty <= 0 ? 'text-red-400' : 'text-yellow-400' }}">
                                        {{ $item->current_qty }}/{{ $item->min_threshold }} {{ $item->unit }}
                                    </span>
                                    <div
                                        class="text-[9px] {{ $item->current_qty <= 0 ? 'text-red-400' : 'text-yellow-400' }}">
                                        {{ $item->current_qty <= 0 ? 'HẾT HÀNG!' : 'THẤP' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-xs text-center py-2">Tồn kho ổn định ✓</p>
                        @endforelse
                    </div>
                </div>

                {{-- Cancelled orders / refunds --}}
                <div class="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-[#FF6B35]">📦</span>
                        <span class="text-white font-black text-sm">Đơn đã hủy</span>
                        <span
                            class="bg-[#FF6B35] text-white text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">{{ $refunds->count() }}</span>
                    </div>
                    <div class="space-y-3">
                        @forelse($refunds as $refund)
                            <div class="rounded-xl p-3 border bg-orange-900/20 border-orange-700/30">
                                <div class="flex items-center justify-between mb-1">
                                    <span
                                        class="text-white text-xs font-black">{{ $refund->order_number }}</span>
                                    <span class="text-[10px] font-black text-orange-400">Đã hủy</span>
                                </div>
                                <div class="text-gray-400 text-[10px] mb-2">
                                    {{ $refund->user?->name }} · {{ $refund->cancelled_reason }}
                                </div>
                                <span
                                    class="text-[#FF6B35] font-black text-sm">{{ number_format($refund->grand_total) }}đ</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-xs text-center py-2">Không có đơn hủy ✓</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

