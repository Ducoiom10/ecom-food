<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory\InventoryItem;
use App\Models\Order\Order;

class KdsController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product', 'items.options.optionValue')
            ->whereIn('status', ['confirmed', 'preparing', 'ready'])
            ->orderBy('priority', 'desc')
            ->orderBy('confirmed_at')
            ->get();

        $inventory = InventoryItem::whereColumn('current_qty', '<=', 'min_threshold')->get();

        return view('admin.dashboard.kds', compact('orders', 'inventory'));
    }

    public function move(int $id)
    {
        $order = Order::findOrFail($id);

        $next = match($order->status) {
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
        InventoryItem::findOrFail($id)->update(request()->only('current_qty'));
        return back();
    }
}
