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
        $selected = $branches->first();

        return view('admin.branch', [
            'branches'       => $branches,
            'selectedBranch' => $selected,
            'hourlyData'     => [],
            'lowStock'       => $selected ? InventoryItem::where('branch_id', $selected->id)->get() : [],
            'refunds'        => [],
        ]);
    }

    public function refund(int $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled', 'cancelled_reason' => request('reason'), 'cancelled_at' => now()]);

        return back();
    }
}
