<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function show(int $id)
    {
        $order = Order::with('items.product', 'branch', 'shipper', 'voucher')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('client.order-tracking', compact('order'));
    }
}
