<?php

namespace App\Observers;

use App\Models\Order\Order;
use App\Models\Order\OrderStatusLog;

class OrderObserver
{
    /**
     * Ghi log khi order được tạo với status ban đầu.
     */
    public function created(Order $order): void
    {
        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => $order->status,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    /**
     * Ghi log khi order đổi status.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            OrderStatusLog::create([
                'order_id'   => $order->id,
                'status'     => $order->status,
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        }
    }
}
