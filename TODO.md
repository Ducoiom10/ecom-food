# Sprint 10 TODO — Cải thiện RBAC phân quyền rõ ràng

## Plan

- [x]   1. Tạo `PermissionMiddleware` (`app/Http/Middleware/PermissionMiddleware.php`)
- [x]   2. Đăng ký alias `permission` trong `bootstrap/app.php`
- [x]   3. Thêm `hasPermission()` vào `User` model
- [x]   4. Cập nhật `SuperAdminController` — mở rộng defaultRolePerms + kiểm tra permission trong action
- [x]   5. Cập nhật `KdsController` — kiểm tra `view_kds` / `update_kds`
- [x]   6. Cập nhật `SmartPrepController` — kiểm tra `view_smartprep` / `update_smartprep`
- [x]   7. Cập nhật `DispatchController` — kiểm tra `view_dispatch` / `update_dispatch` / `assign_shipper`
- [x]   8. Cập nhật `BranchController` — kiểm tra `view_branch` / `refund_orders`
- [x]   9. Cập nhật `routes/web.php` — thay `role:` bằng `permission:` cho từng route group
- [x]   10. Cập nhật `layouts/admin.blade.php` — sidebar hiển thị theo permission
- [x]   11. Cập nhật `admin/dashboard/super.blade.php` — tabs hiển thị theo permission
- [x]   12. Kiểm tra routes & test

## Kết quả

### Permission Matrix

| Permission           | super_admin | branch_manager | coordinator | kitchen_staff | support |
| -------------------- | ----------- | -------------- | ----------- | ------------- | ------- |
| `view_kds`           | ✅          | ✅             | ❌          | ✅            | ❌      |
| `update_kds`         | ✅          | ❌             | ❌          | ✅            | ❌      |
| `view_smartprep`     | ✅          | ✅             | ❌          | ✅            | ❌      |
| `update_smartprep`   | ✅          | ❌             | ❌          | ✅            | ❌      |
| `view_dispatch`      | ✅          | ❌             | ✅          | ❌            | ❌      |
| `update_dispatch`    | ✅          | ❌             | ✅          | ❌            | ❌      |
| `assign_shipper`     | ✅          | ❌             | ✅          | ❌            | ❌      |
| `view_branch`        | ✅          | ✅             | ❌          | ❌            | ❌      |
| `refund_orders`      | ✅          | ✅             | ❌          | ❌            | ✅      |
| `view_revenue`       | ✅          | ✅             | ❌          | ❌            | ❌      |
| `manage_vouchers`    | ✅          | ❌             | ❌          | ❌            | ❌      |
| `send_push`          | ✅          | ❌             | ❌          | ❌            | ❌      |
| `manage_permissions` | ✅          | ❌             | ❌          | ❌            | ❌      |
| `view_audit_log`     | ✅          | ❌             | ❌          | ❌            | ❌      |

### Các thay đổi chính

1. **PermissionMiddleware** — kiểm tra `RolePermission::has()` thay vì chỉ so sánh role name. `super_admin` luôn bypass.
2. **User::hasPermission()** — method tiện ích để kiểm tra quyền ở controller & Blade view.
3. **Routes** — mỗi route group admin được bảo vệ bằng `permission:` middleware phù hợp.
4. **Controllers** — mỗi action kiểm tra `abort_unless(auth()->user()->hasPermission(...), 403)` để đảm bảo defense in depth.
5. **Sidebar** — chỉ hiển thị nav item nếu user có permission tương ứng.
6. **Super Admin Tabs** — Analytics, Campaigns, RBAC, Audit chỉ hiện khi có permission tương ứng.
