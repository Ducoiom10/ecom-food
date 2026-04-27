# Sprint 9 TODO

## Plan

- [x]   1. Tạo TODO.md (theo dõi tiến độ)
- [x]   2. RBAC Permissions DB
    - [x] 2.1 Migration `role_permissions`
    - [x] 2.2 Model `RolePermission`
    - [x] 2.3 Update `SuperAdminController::updatePerm` lưu DB
    - [x] 2.4 Update `SuperAdminController::index` load từ DB
    - [x] 2.5 Update `super.blade.php` cho phép chỉnh sửa matrix
- [x]   3. Guest Order Tracking
    - [x] 3.1 Tạo `OrderTrackingController`
    - [x] 3.2 Routes public `/track-order` (GET/POST)
    - [x] 3.3 View form tìm kiếm + reuse detail view
- [x]   4. Notification System
    - [x] 4.1 `NotificationController`
    - [x] 4.2 Routes `/notifications` + mark-read
    - [x] 4.3 Update header dropdown notifications
- [x]   5. Order Status Logs
    - [x] 5.1 Migration `order_status_logs`
    - [x] 5.2 Model `OrderStatusLog`
    - [x] 5.3 Observer ghi log tự động
- [x]   6. Model Observers & Audit
    - [x] 6.1 Tạo `OrderObserver`
    - [x] 6.2 Đăng ký trong `AppServiceProvider`
- [x]   7. Feature Tests
    - [x] 7.1 `CartTest`
    - [x] 7.2 `OrderTest`
    - [x] 7.3 `GroupOrderTest`
- [x]   8. Commit & Push Sprint 9
