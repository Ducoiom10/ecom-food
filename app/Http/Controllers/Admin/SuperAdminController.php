<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendPushRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Models\Order\Order;
use App\Models\Promotion\PushCampaign;
use App\Models\Promotion\Voucher;
use App\Models\System\AuditLog;
use App\Models\System\Branch;
use App\Models\System\RolePermission;

class SuperAdminController extends Controller
{
    private function defaultRolePerms(): array
    {
        return [
            'super_admin'    => ['view_revenue', 'manage_menu', 'manage_vouchers', 'view_orders', 'update_orders', 'manage_staff', 'refund_orders', 'view_audit_log'],
            'branch_manager' => ['view_revenue', 'manage_menu', 'view_orders', 'update_orders', 'refund_orders'],
            'coordinator'    => ['view_orders', 'update_orders'],
            'kitchen_staff'  => ['view_orders', 'update_orders'],
            'support'        => ['view_orders', 'refund_orders'],
        ];
    }

    private function getRolePerms(): array
    {
        $dbMatrix = RolePermission::matrix();
        return empty($dbMatrix) ? $this->defaultRolePerms() : $dbMatrix;
    }

    public function index()
    {
        $totalRevenue = Order::where('status', 'completed')->sum('grand_total');

        $revenueData = Order::where('status', 'completed')
            ->selectRaw("DAYOFWEEK(created_at) - 1 as day, SUM(grand_total) as revenue")
            ->groupByRaw("DAYOFWEEK(created_at) - 1")
            ->get()
            ->map(fn($r) => ['day' => ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'][$r->day] ?? '?', 'revenue' => $r->revenue]);

        $permissions = [
            ['key' => 'view_revenue',    'label' => 'Xem doanh thu'],
            ['key' => 'manage_menu',     'label' => 'Quản lý thực đơn'],
            ['key' => 'manage_vouchers', 'label' => 'Quản lý voucher'],
            ['key' => 'view_orders',     'label' => 'Xem đơn hàng'],
            ['key' => 'update_orders',   'label' => 'Cập nhật đơn hàng'],
            ['key' => 'manage_staff',    'label' => 'Quản lý nhân viên'],
            ['key' => 'refund_orders',   'label' => 'Hoàn tiền'],
            ['key' => 'view_audit_log',  'label' => 'Xem audit log'],
        ];

        return view('admin.dashboard.super', [
            'activeTab'    => request('tab', 'analytics'),
            'totalRevenue' => $totalRevenue,
            'revenueData'  => $revenueData,
            'branches'     => Branch::all(),
            'vouchers'     => Voucher::latest()->get(),
            'roles'        => ['super_admin', 'branch_manager', 'coordinator', 'kitchen_staff', 'support'],
            'permissions'  => $permissions,
            'rolePerms'    => $this->getRolePerms(),
            'auditLogs'    => AuditLog::with('user')->latest()->take(50)->get(),
        ]);
    }

    public function sendPush(SendPushRequest $request)
    {
        PushCampaign::create([
            'title'      => $request->title,
            'body'       => $request->body,
            'segment'    => $request->segment,
            'created_by' => auth()->id(),
            'sent_at'    => now(),
        ]);

        return back()->with('success', 'Đã gửi push notification.');
    }

    public function updatePerm(UpdatePermissionRequest $request)
    {
        RolePermission::updateOrCreate(
            ['role' => $request->role, 'permission' => $request->permission],
            ['is_allowed' => $request->allowed]
        );

        AuditLog::record('UPDATE', 'role_permissions', 0, null, [
            'role'       => $request->role,
            'permission' => $request->permission,
            'is_allowed' => $request->allowed,
        ]);

        return response()->json(['ok' => true]);
    }
}
