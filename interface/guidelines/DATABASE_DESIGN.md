# Database Design Review — Ba Anh Em F&B Ecosystem

> Đánh giá bởi: Backend Architect  
> Phiên bản schema: v1.0 → v1.1 (đề xuất)  
> Cập nhật: 2026

---

## Mục lục

1. [Schema Hiện Tại (v1.0)](#1-schema-hiện-tại-v10)
2. [Đánh Giá Chức Năng](#2-đánh-giá-chức-năng)
3. [Schema Đề Xuất Bổ Sung (v1.1)](#3-schema-đề-xuất-bổ-sung-v11)
4. [Phân Tích Luồng Quan Trọng](#4-phân-tích-luồng-quan-trọng)
5. [Vấn Đề Kỹ Thuật](#5-vấn-đề-kỹ-thuật)
6. [Tổng Kết & Ưu Tiên](#6-tổng-kết--ưu-tiên)

---

## 1. Schema Hiện Tại (v1.0)

### A. Nhóm Core & Menu

```sql
branches:              id, name, address, lat, lng, status (open/closed)
categories:            id, name, slug, icon, priority
products:              id, category_id, name, slug, base_price, image, description, is_active
product_options:       id, product_id, name (Size/Topping), type (required/optional)
product_option_values: id, option_id, label (M/L/Trân châu), extra_price
```

### B. Nhóm Orders & Real-time

```sql
group_rooms:       id, branch_id, host_id, room_code (Index), is_locked, status (active/completed)
participants:      id, room_id, user_id (nullable), display_name, emoji, is_paid
orders:            id, order_number (Unique), user_id, branch_id, group_room_id (nullable),
                   status (Enum: pending/confirmed/preparing/ready/delivering/completed/cancelled),
                   delivery_mode (pickup/delivery), payment_method, grand_total
order_items:       id, order_id, participant_id (nullable), product_id, quantity, price, note
order_item_options: id, order_item_id, option_value_id
```

### C. Nhóm Operations & AI

```sql
inventory_items:        id, branch_id, sku, name, unit, current_qty, max_qty, min_threshold
inventory_transactions: id, inventory_item_id, type (sale/waste/import), quantity, reference_id
shippers:               id, user_id, current_lat, current_lng, status (free/busy)
smart_prep_logs:        id, branch_id, inventory_item_id, predicted_qty, weather_data (JSON), urgency
audit_logs:             id, user_id, action, table_name, row_id, old_values (JSON), new_values (JSON), ip_address
```

---

## 2. Đánh Giá Chức Năng

### ✅ Điểm Mạnh

| Điểm | Lý do |
|------|-------|
| Tách `product_options` / `product_option_values` | Giải quyết Size/Topping linh hoạt, không hardcode |
| `group_rooms` → `participants` → `order_items.participant_id` | Luồng Group Order rõ ràng |
| `audit_logs` với `old_values/new_values JSON` | Đúng chuẩn enterprise, đáp ứng SuperAdmin Audit Trail |
| `inventory_transactions` dạng ledger (append-only) | An toàn cho concurrency, dễ rollback |

### ❌ Thiếu Sót Chức Năng

#### 1. Không có `users`, `vouchers`, `loyalty` — Block trực tiếp 3 màn hình

- **ProfilePage** cần: Snack Points, VIP tier, order history
- **CartPage** cần: Voucher Hub (applied/ineligible states)
- **SuperAdminPage** cần: Campaign Manager, segment targeting

#### 2. `orders` thiếu nhiều trường — Block CartPage & DispatchPage

- Thiếu: `subtotal`, `discount_amount`, `shipping_fee`, `voucher_id`
- Thiếu: `delivery_address`, `scheduled_at` (Delivery Scheduler)
- Thiếu: `shipper_id`, `estimated_eta`, `cancelled_reason`

#### 3. Không có timestamp tracking trong `orders` — Block KDSPage

- KDS hiển thị `elapsed` time nhưng không có `confirmed_at`, `preparing_at`, `ready_at`

#### 4. Không có `combos` — Block HomePage Combo Section

- `COMBOS` trong mockData có `items: ["1", "2"]` nhưng schema không có bảng tương ứng

#### 5. `smart_prep_logs` thiếu tracking xác nhận — Block SmartPrepPage

- Nút "Đã làm" cần lưu ai xác nhận, lúc nào

#### 6. Không có `notifications` / `push_campaigns` — Block SuperAdminPage

- UI "Gửi Push Notification" với segment targeting không có bảng backend

---

## 3. Schema Đề Xuất Bổ Sung (v1.1)

### 3.1 Nhóm Users & Auth

```sql
users (
  id              BIGINT PRIMARY KEY,
  name            VARCHAR(100) NOT NULL,
  phone           VARCHAR(15) UNIQUE NOT NULL,
  email           VARCHAR(100) UNIQUE,
  password_hash   VARCHAR(255),
  role            ENUM('super_admin','branch_manager','coordinator','kitchen_staff','support','customer'),
  snack_points    INT DEFAULT 0,
  tier            ENUM('bronze','silver','gold','vip') DEFAULT 'bronze',
  avatar_url      VARCHAR(255),
  is_active       BOOLEAN DEFAULT TRUE,
  created_at      TIMESTAMP,
  updated_at      TIMESTAMP
)

user_addresses (
  id              BIGINT PRIMARY KEY,
  user_id         BIGINT NOT NULL REFERENCES users(id),
  label           VARCHAR(50),   -- "Nhà", "Văn phòng"
  address         TEXT NOT NULL,
  lat             DECIMAL(10,8),
  lng             DECIMAL(11,8),
  is_default      BOOLEAN DEFAULT FALSE
)
```

### 3.2 Nhóm Vouchers & Loyalty

```sql
vouchers (
  id              BIGINT PRIMARY KEY,
  code            VARCHAR(50) UNIQUE NOT NULL,
  type            ENUM('flat','percent','shipping') NOT NULL,
  value           DECIMAL(10,2) NOT NULL,   -- số tiền hoặc %
  min_order       DECIMAL(10,2) DEFAULT 0,
  max_discount    DECIMAL(10,2),            -- cap cho type=percent
  max_uses        INT,
  used_count      INT DEFAULT 0,
  expires_at      TIMESTAMP,
  is_active       BOOLEAN DEFAULT TRUE,
  created_by      BIGINT REFERENCES users(id)
)

voucher_usages (
  id              BIGINT PRIMARY KEY,
  voucher_id      BIGINT NOT NULL REFERENCES vouchers(id),
  user_id         BIGINT NOT NULL REFERENCES users(id),
  order_id        BIGINT NOT NULL REFERENCES orders(id),
  discount_applied DECIMAL(10,2) NOT NULL,
  used_at         TIMESTAMP DEFAULT NOW()
)

loyalty_challenges (
  id              BIGINT PRIMARY KEY,
  title           VARCHAR(200) NOT NULL,
  description     TEXT,
  points_reward   INT NOT NULL,
  target_count    INT NOT NULL,
  type            ENUM('order_streak','lunch_order','try_new','referral'),
  is_active       BOOLEAN DEFAULT TRUE
)

user_challenge_progress (
  id              BIGINT PRIMARY KEY,
  user_id         BIGINT NOT NULL REFERENCES users(id),
  challenge_id    BIGINT NOT NULL REFERENCES loyalty_challenges(id),
  current_count   INT DEFAULT 0,
  completed_at    TIMESTAMP,
  UNIQUE(user_id, challenge_id)
)
```

### 3.3 Nhóm Combos

```sql
combos (
  id              BIGINT PRIMARY KEY,
  name            VARCHAR(200) NOT NULL,
  description     TEXT,
  combo_price     DECIMAL(10,2) NOT NULL,
  original_price  DECIMAL(10,2) NOT NULL,
  image           VARCHAR(255),
  is_active       BOOLEAN DEFAULT TRUE
)

combo_items (
  id              BIGINT PRIMARY KEY,
  combo_id        BIGINT NOT NULL REFERENCES combos(id),
  product_id      BIGINT NOT NULL REFERENCES products(id),
  quantity        INT DEFAULT 1
)
-- Khi checkout combo: backend decompose → tạo order_items cho từng product
```

### 3.4 Cập Nhật Bảng `orders`

```sql
-- Thêm vào orders:
subtotal          DECIMAL(10,2) NOT NULL,
discount_amount   DECIMAL(10,2) DEFAULT 0,
shipping_fee      DECIMAL(10,2) DEFAULT 0,
voucher_id        BIGINT REFERENCES vouchers(id),
delivery_address  TEXT,
delivery_lat      DECIMAL(10,8),
delivery_lng      DECIMAL(11,8),
scheduled_at      TIMESTAMP,              -- Delivery Scheduler
shipper_id        BIGINT REFERENCES shippers(id),
estimated_eta     INT,                    -- phút
cancelled_reason  TEXT,

-- Timestamp tracking cho KDS elapsed time
confirmed_at      TIMESTAMP,
preparing_at      TIMESTAMP,
ready_at          TIMESTAMP,
completed_at      TIMESTAMP,
cancelled_at      TIMESTAMP
```

### 3.5 Cập Nhật Bảng `shippers`

```sql
-- Thêm vào shippers:
name              VARCHAR(100) NOT NULL,
phone             VARCHAR(15) NOT NULL,
active_order_count INT DEFAULT 0          -- cache, update khi assign/complete
```

### 3.6 Cập Nhật `smart_prep_logs`

```sql
-- Thêm vào smart_prep_logs:
action_text       TEXT,                   -- "Luộc sẵn 35 vắt mì ngay!"
acknowledged_by   BIGINT REFERENCES users(id),
acknowledged_at   TIMESTAMP
```

### 3.7 Nhóm Notifications & Campaigns

```sql
push_campaigns (
  id              BIGINT PRIMARY KEY,
  title           VARCHAR(200) NOT NULL,
  body            TEXT NOT NULL,
  segment         ENUM('all','abandoned_cart','inactive_7d','vip'),
  sent_count      INT DEFAULT 0,
  created_by      BIGINT REFERENCES users(id),
  sent_at         TIMESTAMP
)

notifications (
  id              BIGINT PRIMARY KEY,
  user_id         BIGINT NOT NULL REFERENCES users(id),
  title           VARCHAR(200) NOT NULL,
  body            TEXT,
  is_read         BOOLEAN DEFAULT FALSE,
  created_at      TIMESTAMP DEFAULT NOW()
)
```

### 3.8 Inventory ↔ Menu Linking

```sql
product_ingredients (
  id                  BIGINT PRIMARY KEY,
  product_id          BIGINT NOT NULL REFERENCES products(id),
  inventory_item_id   BIGINT NOT NULL REFERENCES inventory_items(id),
  quantity_per_unit   DECIMAL(10,3) NOT NULL   -- 0.5 kg mì cho 1 tô
)
-- Khi inventory_item hết → tự động đánh dấu product không available
-- Hoặc MVP: thêm is_available vào products để kitchen toggle thủ công
```

---

## 4. Phân Tích Luồng Quan Trọng

### Luồng 1: Group Order → Split Bill ⚠️ VẤN ĐỀ THIẾT KẾ

**Vấn đề hiện tại:**

```
group_rooms
    └── participants (room_id, user_id nullable)
orders (group_room_id)
    └── order_items (participant_id)  ← 1 order chứa items của nhiều participant
```

Khi Split Bill cần tính "tổng của từng người" → phải GROUP BY `participant_id` trong `order_items`, nhưng `order_items` thuộc về `orders` — không rõ ràng về ownership.

**Đề xuất — Phương án A (Khuyến nghị):**

```
group_rooms
    └── participants
            └── orders (participant_id FK)  ← mỗi participant = 1 order riêng
                    └── order_items
```

```sql
-- Thêm vào orders:
participant_id    BIGINT REFERENCES participants(id)  -- nullable, chỉ dùng trong group order
```

Phù hợp với `SplitBill.tsx` đang render `bills.map(bill => ...)` theo từng participant.

**Đề xuất — Phương án B (Nếu giữ 1 order/group):**

```sql
participant_bills (
  id              BIGINT PRIMARY KEY,
  group_room_id   BIGINT NOT NULL REFERENCES group_rooms(id),
  participant_id  BIGINT NOT NULL REFERENCES participants(id),
  total_amount    DECIMAL(10,2) NOT NULL,  -- tính từ order_items
  is_paid         BOOLEAN DEFAULT FALSE,
  paid_at         TIMESTAMP,
  payment_method  ENUM('momo','bank','cod','zalopay')
)
```

---

### Luồng 2: KDS Real-time

```
orders (status: confirmed → preparing → ready → completed)
    └── order_items
            └── order_item_options → option_value_id → product_option_values.label
```

**Yêu cầu từ KDSPage:**
- Hiển thị `elapsed` time → cần `confirmed_at` hoặc `preparing_at`
- Hiển thị toppings → join `order_item_options` → `product_option_values`
- Priority flag → cần thêm `priority ENUM('normal','high')` vào `orders`

```sql
-- Thêm vào orders:
priority          ENUM('normal','high') DEFAULT 'normal'
```

**Option nâng cao** (nếu cần audit đầy đủ trạng thái):

```sql
order_status_logs (
  id              BIGINT PRIMARY KEY,
  order_id        BIGINT NOT NULL REFERENCES orders(id),
  status          ENUM(...) NOT NULL,
  changed_by      BIGINT REFERENCES users(id),
  changed_at      TIMESTAMP DEFAULT NOW()
)
```

---

### Luồng 3: Dispatch & Shipper Assignment

```
orders (status: ready → delivering → completed)
    └── shipper_id → shippers
            └── user_id → users (name, phone)
```

**Yêu cầu từ DispatchPage:**
- Live map markers → `shippers.current_lat/lng` (cần update realtime qua WebSocket)
- Assign shipper → `orders.shipper_id = shippers.id`, `orders.status = 'picking'`
- Batch orders → query `orders WHERE status IN ('ready','picking') AND branch_id = ?`

---

### Luồng 4: Inventory → Smart Prep → KDS

```
inventory_items (current_qty, min_threshold)
    ↓ trigger khi current_qty < min_threshold
smart_prep_logs (predicted_qty, urgency, weather_data)
    ↓ staff xác nhận
smart_prep_logs (acknowledged_by, acknowledged_at)
    ↓ khi order completed
inventory_transactions (type: 'sale', reference_id: order_id)
    ↓ cập nhật
inventory_items.current_qty -= quantity
```

---

### Luồng 5: Checkout với Concurrency Control

```
User bấm "Đặt hàng"
    → BEGIN TRANSACTION
    → SELECT inventory_items FOR UPDATE (Pessimistic Lock)
    → Kiểm tra current_qty đủ không
    → Tạo orders + order_items
    → Trừ inventory_items.current_qty
    → Áp dụng voucher (tăng vouchers.used_count)
    → Tạo voucher_usages
    → COMMIT
```

---

## 5. Vấn Đề Kỹ Thuật

| # | Vấn đề | Hiện tại | Đề xuất |
|---|--------|----------|---------|
| 1 | `group_rooms.host_id` FK về đâu? | Không rõ | Nên là `participants.id` để nhất quán |
| 2 | `participants.user_id nullable` | Không có constraint | Thêm: `CHECK (user_id IS NOT NULL OR display_name IS NOT NULL)` |
| 3 | `inventory_transactions.reference_id` | Polymorphic không có type | Thêm `reference_type ENUM('order','manual','import','waste')` |
| 4 | `smart_prep_logs.weather_data JSON` | Schema không cố định | Tách thành: `weather_condition`, `temperature`, `delivery_boost_pct` |
| 5 | Missing indexes | Chỉ đề cập `room_code` | Thêm index: `orders(status)`, `orders(branch_id)`, `orders(created_at)`, `inventory_items(branch_id)` |
| 6 | `orders.order_number` | Unique string | Nên có format: `BAE-{branch_code}-{YYYYMMDD}-{seq}` |
| 7 | Không có `soft delete` | Hard delete | Thêm `deleted_at` cho `products`, `vouchers`, `users` |

---

## 6. Tổng Kết & Ưu Tiên

### Mức độ hoàn chỉnh hiện tại: ~60%

```
🔴 PHẢI CÓ — Block chức năng ngay (Sprint 1):
   ✗ Bảng users (block toàn bộ auth, profile, RBAC)
   ✗ Bảng vouchers + voucher_usages (block CartPage)
   ✗ Timestamp columns trong orders: confirmed_at, preparing_at, ready_at (block KDS elapsed)
   ✗ Làm rõ luồng participant → order (Phương án A)
   ✗ Bảng combos + combo_items (block HomePage)
   ✗ orders thêm: subtotal, discount_amount, shipping_fee, voucher_id, shipper_id

🟡 NÊN CÓ — Ảnh hưởng UX (Sprint 2):
   ✗ loyalty_challenges + user_challenge_progress (ProfilePage rewards tab)
   ✗ notifications + push_campaigns (SuperAdmin campaigns)
   ✗ product_ingredients (inventory ↔ menu auto-disable)
   ✗ user_addresses (CartPage delivery address)
   ✗ smart_prep_logs thêm acknowledged_by/at
   ✗ shippers thêm name, phone
   ✗ orders thêm priority, scheduled_at, cancelled_reason

🟢 TỐI ƯU SAU — Không block MVP (Sprint 3+):
   ✗ order_status_logs (thay timestamp columns nếu cần audit đầy đủ)
   ✗ Tách weather_data JSON thành columns riêng
   ✗ participant_bills (nếu chọn Phương án B cho Split Bill)
   ✗ Soft delete (deleted_at) cho các bảng quan trọng
   ✗ Geo-spatial index (PostGIS) cho branch nearest query
```

### Sơ đồ quan hệ tổng thể (sau v1.1)

```
users ──────────────────────────────────────────────────────┐
  │                                                          │
  ├── user_addresses                                         │
  ├── user_challenge_progress → loyalty_challenges           │
  ├── notifications                                          │
  │                                                          │
  ├── orders ──────────────────────────────────────────┐    │
  │     │  └── voucher_id → vouchers                   │    │
  │     │  └── shipper_id → shippers                   │    │
  │     │  └── branch_id  → branches                   │    │
  │     │  └── participant_id → participants ←──┐      │    │
  │     │                                       │      │    │
  │     └── order_items                    group_rooms │    │
  │           └── order_item_options            │      │    │
  │                 └── product_option_values   │      │    │
  │                       └── product_options   │      │    │
  │                             └── products    │      │    │
  │                                   └── categories  │    │
  │                                   └── combos ──────┘    │
  │                                         └── combo_items  │
  │                                                          │
  ├── inventory_items → branches                             │
  │     └── inventory_transactions                           │
  │     └── product_ingredients → products                   │
  │                                                          │
  └── smart_prep_logs → inventory_items                      │
        └── acknowledged_by ────────────────────────────────┘

audit_logs → users (any action on critical tables)
push_campaigns → users (created_by)
voucher_usages → vouchers + users + orders
```

---

*Tài liệu này được tổng hợp dựa trên phân tích giao diện hiện tại tại `interface/src/app/components/` và yêu cầu nghiệp vụ từ `ba-anh-em-app-design.md`.*
