<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryItem;
use App\Models\Order\Order;
use App\Models\System\Branch;

class BranchController extends Controller
{
    public function index()
    {
        $branches   = Branch::all();
        $selectedId = request('branch', $branches->first()?->id);
        $selected   = Branch::find($selectedId);

        $todayOrders = $selected
            ? Order::where('branch_id', $selected->id)->whereDate('created_at', today())->get()
            : collect();

        $revenue    = $todayOrders->where('status', 'completed')->sum('grand_total');
        $orderCount = $todayOrders->count();

        $hourlyData = $todayOrders->where('status', 'completed')
            ->groupBy(fn($o) => $o->created_at->format('H:00'))
            ->map(fn($g, $h) => ['hour' => $h, 'total' => $g->count()])
            ->values();

        $lowStock = $selected
            ? InventoryItem::where('branch_id', $selected->id)
                ->whereColumn('current_qty', '<=', 'min_threshold')
                ->get()
            : collect();

        $refunds = $selected
            ? Order::where('branch_id', $selected->id)
                ->where('status', 'cancelled')
                ->whereNotNull('cancelled_reason')
                ->with('user')
                ->latest()
                ->take(10)
                ->get()
            : collect();

        // Đơn chờ duyệt (pending) — branch_manager / support có thể duyệt
        $pendingOrders = $selected
            ? Order::where('branch_id', $selected->id)
                ->where('status', 'pending')
                ->with('user', 'items.product')
                ->latest()
                ->get()
            : collect();

        $pendingCount = $pendingOrders->count();

        return view('admin.branches.index', [
            'branches'       => $branches,
            'selectedBranch' => $selected,
            'revenue'        => $revenue,
            'orderCount'     => $orderCount,
            'avgRating'      => 4.8,
            'hourlyData'     => $hourlyData,
            'lowStock'       => $lowStock,
            'refunds'        => $refunds,
            'pendingOrders'  => $pendingOrders,
            'pendingCount'   => $pendingCount,
        ]);
    }

    /**
     * Duyệt đơn hàng: chuyển từ pending → confirmed
     */
    public function confirmOrder(int $id)
    {
        abort_unless(auth()->user()->hasPermission('update_orders'), 403);

        $order = Order::findOrFail($id);
        abort_if($order->status !== 'pending', 400, 'Đơn hàng không ở trạng thái chờ duyệt.');

        $order->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Đã duyệt đơn #' . $order->order_number);
    }

    public function refund(int $id)
    {
        Order::findOrFail($id)->update([
            'status'           => 'cancelled',
            'cancelled_reason' => request('reason', 'Hoàn tiền theo yêu cầu'),
            'cancelled_at'     => now(),
        ]);

        return back()->with('success', 'Đã xử lý hoàn tiền.');
    }
}
