<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Hiển thị form tra cứu đơn hàng (không cần đăng nhập).
     */
    public function showForm()
    {
        return view('client.order-tracking-form');
    }

    /**
     * Xử lý tra cứu đơn hàng bằng order_number + phone.
     */
    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'phone'        => 'required|string',
        ]);

        $order = Order::with('items.product', 'branch', 'shipper', 'voucher')
            ->where('order_number', $request->order_number)
            ->whereHas('user', fn($q) => $q->where('phone', $request->phone))
            ->first();

        if (!$order) {
            return back()->withErrors(['track' => 'Không tìm thấy đơn hàng. Vui lòng kiểm tra lại mã đơn và số điện thoại.']);
        }

        return view('client.order-tracking', compact('order'));
    }
}

