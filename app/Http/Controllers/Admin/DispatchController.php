<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipper;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    public function index()
    {
        $orders  = Order::with('shipper', 'user')
            ->whereIn('status', ['ready', 'delivering'])
            ->latest()
            ->get();

        $shippers = Shipper::all();

        return view('admin.dispatch', [
            'orders'        => $orders,
            'shippers'      => $shippers,
            'activeCount'   => $orders->count(),
            'freeShippers'  => $shippers->where('status', 'free')->count(),
            'todayCount'    => Order::whereDate('created_at', today())->count(),
            'batchableCount'=> $orders->where('status', 'ready')->count(),
        ]);
    }

    public function update(int $id)
    {
        Order::findOrFail($id)->update(request()->only('status', 'estimated_eta'));
        return back();
    }

    public function assign(Request $request)
    {
        $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'shipper_id' => 'required|exists:shippers,id',
        ]);

        $order   = Order::findOrFail($request->order_id);
        $shipper = Shipper::findOrFail($request->shipper_id);

        $order->update(['shipper_id' => $shipper->id, 'status' => 'delivering']);
        $shipper->increment('active_order_count');
        $shipper->update(['status' => 'busy']);

        return back();
    }
}
