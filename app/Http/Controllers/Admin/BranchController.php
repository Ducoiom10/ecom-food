<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\Order;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $selectedId = request('branch', $branches->first()?->id);
        $selected = Branch::find($selectedId);

        $todayOrders = $selected
            ? Order::where('branch_id', $selected->id)->whereDate('created_at', today())->get()
            : collect();

        $revenue    = $todayOrders->where('status', 'completed')->sum('grand_total');
        $orderCount = $todayOrders->count();
        $avgRating  = 4.8; // TODO: từ reviews table Sprint 3

        // Doanh thu theo giờ
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

        return view('admin.branch', [
            'branches'       => $branches,
            'selectedBranch' => $selected,
            'revenue'        => $revenue,
            'orderCount'     => $orderCount,
            'avgRating'      => $avgRating,
            'hourlyData'     => $hourlyData,
            'lowStock'       => $lowStock,
            'refunds'        => $refunds,
        ]);
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
