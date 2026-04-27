# Sprint 11 TODO — Cải thiện UI hoàn thiện & chức năng thực

## Các vấn đề đã sửa

### 1. Khoảng cách địa chỉ không rõ ràng ✅

- **Trước:** Tất cả món ăn hardcode `distance: "0.8km"`, không có logic tính toán
- **Sau:**
    - Tạo `interface/src/app/utils/distance.ts` — Haversine formula tính khoảng cách từ tọa độ GPS
    - Thêm `lat/lng` cho từng chi nhánh trong `useBranch.ts`
    - Khoảng cách hiển thị động theo chi nhánh được chọn trên HomePage, MenuPage, CartPage, Layout
    - Phí ship tính động theo khoảng cách (`calculateShippingFee`)
    - ETA giao hàng ước tính theo khoảng cách (`estimateDeliveryTime`)

### 2. Nhiều element lặp không có tính năng ✅

- **Search bar** — Layout.tsx: Giờ đây có thể nhập và Enter để tìm kiếm, chuyển hướng sang `/menu?search=...`
- **Branch selector** — Layout.tsx: Chọn chi nhánh thay đổi khoảng cách thực, toast thông báo
- **Notification badge** — Layout.tsx: Hiển thị số thông báo chưa đọc động, dropdown click để đánh dấu đã đọc
- **Moods filter** — HomePage.tsx: Đã xóa (không có tính năng filter thực)
- **Settings buttons** — ProfilePage.tsx: Thêm toast feedback "Tính năng sắp ra mắt"
- **"Đặt lại" đơn hàng** — ProfilePage.tsx: Thêm các món vào giỏ hàng thực sự
- **Upsell "+ Thêm"** — CartPage.tsx: Hoạt động, thêm món vào giỏ
- **Voucher input** — CartPage.tsx: Có thể nhập mã voucher tay, kiểm tra hợp lệ
- **Checkout button** — CartPage.tsx: Mở modal xác nhận, clear giỏ, toast thành công, chuyển về trang chủ

### 3. Giỏ hàng không đồng bộ ✅

- Tạo `CartContext.tsx` — Context tập trung quản lý giỏ hàng, lưu localStorage
- `routes.tsx` — Wrap app với `CartProvider`
- HomePage, MenuPage, CartPage, ProfilePage đều dùng `useCartContext()`
- Cart count ở bottom nav đồng bộ tự động

### 4. UI Admin cải thiện ✅

- **DispatchPage.tsx:**
    - Tính khoảng cách shipper → đơn hàng bằng Haversine
    - Tính ETA động dựa trên khoảng cách từ nhà hàng
    - Gợi ý shipper rảnh gần nhất cho từng đơn
- **KDSPage.tsx:** Fix new order alert chạy liên tục (`setInterval` thay vì `setTimeout`)

---

## Files đã tạo/sửa

| File                                                    | Thay đổi                                                                         |
| ------------------------------------------------------- | -------------------------------------------------------------------------------- |
| `interface/src/app/utils/distance.ts`                   | 🆕 Haversine, estimateDeliveryTime, calculateShippingFee                         |
| `interface/src/app/hooks/useBranch.ts`                  | 🆕 Quản lý chi nhánh + tính khoảng cách động                                     |
| `interface/src/app/context/CartContext.tsx`             | 🆕 Cart context tập trung                                                        |
| `interface/src/app/hooks/useCart.ts`                    | 🆕 Hook cart (backup, context là chính)                                          |
| `interface/src/app/data/mockData.ts`                    | ✅ Thêm lat/lng cho branches                                                     |
| `interface/src/app/routes.tsx`                          | ✅ Wrap ClientWrapper với CartProvider                                           |
| `interface/src/app/components/Layout.tsx`               | ✅ Search hoạt động, branch selector động, notification dropdown                 |
| `interface/src/app/components/customer/HomePage.tsx`    | ✅ Xóa moods filter, khoảng cách động, toast add to cart                         |
| `interface/src/app/components/customer/MenuPage.tsx`    | ✅ Đồng bộ cart, search params, khoảng cách động                                 |
| `interface/src/app/components/customer/CartPage.tsx`    | ✅ Voucher nhập tay, upsell hoạt động, checkout modal, phí ship theo khoảng cách |
| `interface/src/app/components/customer/ProfilePage.tsx` | ✅ Reorder hoạt động, settings toast feedback                                    |
| `interface/src/app/components/admin/DispatchPage.tsx`   | ✅ Khoảng cách Haversine, ETA động, gợi ý shipper                                |
| `interface/src/app/components/admin/KDSPage.tsx`        | ✅ Fix new order alert liên tục                                                  |

---

## Lưu ý

- Các lỗi TypeScript `JSX element implicitly has type 'any'` và `Cannot find module 'react'` là do project chưa cài `@types/react` — cần chạy `npm install` trong thư mục `interface/` để cài dependencies.
- Các logic tính toán khoảng cách dùng tọa độ mặc định của user (Q1, TP.HCM). Trong production sẽ lấy từ GPS.
