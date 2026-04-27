<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryItem;
use App\Models\Order\Order;

class KdsController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('view_kds'), 403, 'Không có quyền truy cập KDS.');

        $orders = Order::with('items.product', 'items.options.optionValue')
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->orderByRaw("FIELD(status, 'pending', 'confirmed', 'preparing', 'ready')")
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->get();

        $inventory = InventoryItem::whereColumn('current_qty', '<=', 'min_threshold')->get();

        return view('admin.dashboard.kds', compact('orders', 'inventory'));
    }

    /**
     * Duyệt đơn từ KDS: pending → confirmed
     */
    public function confirm(int $id)
    {
        abort_unless(auth()->user()->hasPermission('update_kds'), 403, 'Không có quyền duyệt đơn.');

        $order = Order::findOrFail($id);
        abort_if($order->status !== 'pending', 400, 'Đơn không ở trạng thái chờ duyệt.');

        $order->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Đã duyệt đơn #' . $order->order_number);
    }

    public function move(int $id)
    {
        abort_unless(auth()->user()->hasPermission('update_kds'), 403, 'Không có quyền cập nhật trạng thái KDS.');

        $order = Order::findOrFail($id);

        $next = match ($order->status) {
            'confirmed' => ['status' => 'preparing', 'preparing_at' => now()],
            'preparing' => ['status' => 'ready',     'ready_at'     => now()],
            'ready'     => ['status' => 'delivering'],
            default     => null,
        };

        if ($next) $order->update($next);

        return back();
    }

    public function updateInventory(int $id)
    {
        abort_unless(auth()->user()->hasPermission('update_kds'), 403, 'Không có quyền cập nhật tồn kho.');

        InventoryItem::findOrFail($id)->update(request()->only('current_qty'));
        return back();
    }
}
