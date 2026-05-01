<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignShipperRequest;
use App\Models\Delivery\Shipper;
use App\Models\Order\Order;

class DispatchController extends Controller
{
    public function index()
    {
        $orders = Order::with('shipper', 'user')
            ->whereIn('status', ['ready', 'delivering'])
            ->latest()
            ->get();

        $shippers = Shipper::all();

        return view('admin.dashboard.dispatch', [
            'orders'         => $orders,
            'shippers'       => $shippers,
            'activeCount'    => $orders->count(),
            'freeShippers'   => $shippers->where('status', 'free')->count(),
            'todayCount'     => Order::whereDate('created_at', today())->count(),
            'batchableCount' => $orders->where('status', 'ready')->count(),
        ]);
    }

    public function update(int $id)
    {
        Order::findOrFail($id)->update(request()->only('status', 'estimated_eta'));
        return back();
    }

    public function assign(AssignShipperRequest $request)
    {
        $order   = Order::findOrFail($request->order_id);
        $shipper = Shipper::findOrFail($request->shipper_id);

        $order->update(['shipper_id' => $shipper->id, 'status' => 'delivering']);
        $shipper->increment('active_order_count');
        $shipper->update(['status' => 'busy']);

        return back();
    }
}
