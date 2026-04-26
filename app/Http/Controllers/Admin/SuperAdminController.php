<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Order;
use App\Models\PushCampaign;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::where('status', 'completed')->sum('grand_total');

        $revenueData = Order::where('status', 'completed')
            ->selectRaw("strftime('%w', created_at) as day, SUM(grand_total) as revenue")
            ->groupByRaw("strftime('%w', created_at)")
            ->get()
            ->map(fn($r) => ['day' => ['CN','T2','T3','T4','T5','T6','T7'][$r->day] ?? '?', 'revenue' => $r->revenue]);

        return view('admin.super', [
            'activeTab'    => request('tab', 'analytics'),
            'totalRevenue' => $totalRevenue,
            'revenueData'  => $revenueData,
            'branches'     => Branch::all(),
            'vouchers'     => Voucher::latest()->get(),
            'roles'        => ['super_admin', 'branch_manager', 'coordinator', 'kitchen_staff', 'support'],
            'permissions'  => [
                ['key' => 'view_revenue',    'label' => 'Xem doanh thu'],
                ['key' => 'manage_menu',     'label' => 'Quản lý thực đơn'],
                ['key' => 'manage_vouchers', 'label' => 'Quản lý voucher'],
                ['key' => 'view_orders',     'label' => 'Xem đơn hàng'],
                ['key' => 'update_orders',   'label' => 'Cập nhật đơn hàng'],
                ['key' => 'manage_staff',    'label' => 'Quản lý nhân viên'],
                ['key' => 'refund_orders',   'label' => 'Hoàn tiền'],
                ['key' => 'view_audit_log',  'label' => 'Xem audit log'],
            ],
            'rolePerms'    => [
                'super_admin'    => ['view_revenue','manage_menu','manage_vouchers','view_orders','update_orders','manage_staff','refund_orders','view_audit_log'],
                'branch_manager' => ['view_revenue','manage_menu','view_orders','update_orders','refund_orders'],
                'coordinator'    => ['view_orders','update_orders'],
                'kitchen_staff'  => ['view_orders','update_orders'],
                'support'        => ['view_orders','refund_orders'],
            ],
            'auditLogs'    => AuditLog::with('user')->latest()->take(50)->get(),
        ]);
    }

    public function sendPush(Request $request)
    {
        $request->validate([
            'title'   => 'required|string',
            'body'    => 'required|string',
            'segment' => 'required|in:all,abandoned_cart,inactive_7d,vip',
        ]);

        PushCampaign::create([
            'title'      => $request->title,
            'body'       => $request->body,
            'segment'    => $request->segment,
            'created_by' => auth()->id(),
            'sent_at'    => now(),
        ]);

        return back()->with('success', 'Đã gửi push notification.');
    }

    public function updatePerm(Request $request)
    {
        // TODO: Sprint 3 — lưu permissions vào DB
        return back();
    }
}
