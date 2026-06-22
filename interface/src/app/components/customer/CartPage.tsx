import { useState } from "react";
import { useNavigate } from "react-router";
import {
  Trash2,
  Tag,
  MapPin,
  Clock,
  CreditCard,
  Zap,
  Plus,
  CheckCircle,
  Pencil,
  X,
} from "lucide-react";
import { MENU_ITEMS, formatPrice, type MenuItem } from "../../data/mockData";
import { useCartContext } from "../../context/CartContext";
import { useBranch } from "../../hooks/useBranch";
import {
  calculateShippingFee,
  estimateDeliveryTime,
} from "../../utils/distance";
import { toast } from "sonner";

// Sample toppings available - in real app would come from API based on product
const AVAILABLE_TOPPINGS = [
  { id: "trung_poacher", name: "Trứng lòng đào", price: 5000 },
  { id: "trung_luoc", name: "Trứng luộc", price: 3000 },
  { id: "thit_bo", name: "Thịt bò", price: 15000 },
  { id: "rau_muong", name: "Rau muống", price: 5000 },
  { id: "nam", name: "Nấm", price: 5000 },
  { id: "tauhu", name: "Tàu hủ", price: 3000 },
  { id: "bot_chien", name: "Bột chiên", price: 8000 },
  { id: "kimchi", name: "Kim chi", price: 5000 },
];

const VOUCHERS = [
  {
    code: "LUNCH15K",
    label: "Giảm 15.000đ cho đơn từ 80k",
    discount: 15000,
    minOrder: 80000,
    type: "flat",
  },
  {
    code: "BANANH20",
    label: "Giảm 20% tối đa 30k",
    discount: 30000,
    minOrder: 50000,
    type: "percent",
  },
  {
    code: "FREESHIP",
    label: "Miễn phí vận chuyển",
    discount: 15000,
    minOrder: 0,
    type: "ship",
  },
];

export function CartPage() {
  const navigate = useNavigate();
  const { cart, addItem, updateQty, removeItem, clearCart, subtotal } = useCartContext();
  const { branch, distance } = useBranch();

  const [appliedVoucher, setAppliedVoucher] = useState<
    (typeof VOUCHERS)[0] | null
  >(null);
  const [showVouchers, setShowVouchers] = useState(false);
  const [voucherInput, setVoucherInput] = useState("");
  const [deliveryMethod, setDeliveryMethod] = useState<"delivery" | "pickup">(
    "delivery"
  );
  const [paymentMethod, setPaymentMethod] = useState("momo");
const [showCheckoutModal, setShowCheckoutModal] = useState(false);
  const [editingItem, setEditingItem] = useState<string | null>(null);
  const [editingToppings, setEditingToppings] = useState<string[]>([]);

  const shippingFee =
    deliveryMethod === "pickup" ? 0 : calculateShippingFee(distance);
  const discount = appliedVoucher?.discount ?? 0;
  const total = subtotal + shippingFee - discount;

  const applyVoucher = (voucher: (typeof VOUCHERS)[0]) => {
    if (subtotal >= voucher.minOrder) {
      setAppliedVoucher(voucher);
      setShowVouchers(false);
      toast.success(`Áp dụng voucher ${voucher.code}`);
    } else {
      toast.error(
        `Cần tối thiểu ${formatPrice(voucher.minOrder)} để dùng voucher`
      );
    }
  };

  const handleVoucherInput = () => {
    const code = voucherInput.trim().toUpperCase();
    const found = VOUCHERS.find((v) => v.code === code);
    if (found) {
      applyVoucher(found);
      setVoucherInput("");
    } else {
      toast.error("Mã voucher không hợp lệ");
    }
  };

  const handleCheckout = () => {
    setShowCheckoutModal(true);
  };

  const confirmCheckout = () => {
    clearCart();
    setShowCheckoutModal(false);
    const isPickup = deliveryMethod === "pickup";
    toast.success("🎉 Đặt hàng thành công!", {
      description: isPickup
        ? `Đơn hàng từ ${branch.name} sẵn sàng để lấy trong ~15 phút`
        : `Đơn hàng từ ${branch.name} sẽ được giao trong ${estimateDeliveryTime(distance)}`,
    });
    setTimeout(() => navigate("/"), 1500);
  };

  // Upsell item
  const upsellItem = MENU_ITEMS.find((m) => m.id === "8");
  const [showUpsell, setShowUpsell] = useState(true);

const handleAddUpsell = () => {
    if (upsellItem) {
      addItem({
        menuItemId: upsellItem.id,
        name: upsellItem.name,
        image: upsellItem.image,
        price: upsellItem.price,
      });
      toast.success(`Đã thêm ${upsellItem.name}`);
    }
  };

  // Open edit modal for a cart item
  const openEditModal = (itemId: string) => {
    const item = cart.find((c) => c.id === itemId);
    if (item) {
      setEditingItem(itemId);
      setEditingToppings(item.toppings || []);
    }
  };

  // Toggle topping in edit mode
  const toggleTopping = (toppingId: string) => {
    setEditingToppings((prev) =>
      prev.includes(toppingId)
        ? prev.filter((id) => id !== toppingId)
        : [...prev, toppingId]
    );
  };

  // Save toppings change
  const saveToppings = () => {
    if (!editingItem) return;

    const item = cart.find((c) => c.id === editingItem);
    if (!item) return;

    // Calculate additional price from toppings
    const toppingPrice = editingToppings.reduce((sum, tid) => {
      const topping = AVAILABLE_TOPPINGS.find((t) => t.id === tid);
      return sum + (topping?.price || 0);
    }, 0);

    // Remove old item and add new with updated toppings
    removeItem(editingItem);
    addItem({
      menuItemId: item.menuItemId,
      name: item.name,
      image: item.image,
      price: item.price + toppingPrice - (item.toppings?.reduce((s, tid) => {
        const t = AVAILABLE_TOPPINGS.find((tp) => tp.id === tid);
        return s + (t?.price || 0);
      }, 0) || 0),
      toppings: editingToppings,
    });

    toast.success("Đã cập nhật món");
    setEditingItem(null);
    setEditingToppings([]);
  };

  if (cart.length === 0 && !showCheckoutModal) {
    return (
      <div className="flex flex-col items-center justify-center py-20 px-4">
        <div className="text-6xl mb-4">🛒</div>
        <p className="font-black text-[#1C1C1C] text-lg">Giỏ hàng trống</p>
        <p className="text-gray-500 text-sm mt-1 mb-4">
          Hãy thêm món ngon từ thực đơn nhé!
        </p>
        <button
          onClick={() => navigate("/menu")}
          className="bg-[#FF6B35] text-white font-black px-6 py-3 rounded-xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-[2px_2px_0px_#1C1C1C] transition-all"
        >
          Khám phá thực đơn →
        </button>
      </div>
    );
  }

  return (
    <div className="pb-4">
      <div className="px-4 pt-4">
        <h1 className="font-black text-[#1C1C1C] text-xl mb-4">
          Giỏ hàng của bạn 🛒
        </h1>

        {/* Free shipping progress */}
        {deliveryMethod === "delivery" && subtotal < 100000 && (
          <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 mb-4">
            <div className="flex items-center gap-2 mb-2">
              <span className="text-lg">🛵</span>
              <p className="text-xs font-bold text-[#1C1C1C]">
                Mua thêm{" "}
                <span className="text-[#FF6B35] font-black">
                  {formatPrice(100000 - subtotal)}
                </span>{" "}
                để được{" "}
                <span className="text-green-600 font-black">
                  miễn phí vận chuyển!
                </span>
              </p>
            </div>
            <div className="relative h-3 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
              <div
                className="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full transition-all"
                style={{ width: `${Math.min(100, (subtotal / 100000) * 100)}%` }}
              />
            </div>
          </div>
        )}

        {/* Delivery mode */}
        <div className="flex gap-2 mb-4">
          <button
            onClick={() => setDeliveryMethod("delivery")}
            className={`flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold transition-all ${
              deliveryMethod === "delivery"
                ? "bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]"
                : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"
            }`}
          >
            🛵 Giao hàng (+{formatPrice(shippingFee)})
          </button>
          <button
            onClick={() => setDeliveryMethod("pickup")}
            className={`flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold transition-all ${
              deliveryMethod === "pickup"
                ? "bg-[#1C1C1C] text-white shadow-[2px_2px_0px_#FF6B35]"
                : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"
            }`}
          >
            🏪 Tự đến lấy (Free)
          </button>
        </div>

{/* Cart items - Scrollable container */}
        <div className="mb-4 max-h-[50vh] overflow-y-auto rounded-2xl border-2 border-[#1C1C1C]">
          <div className="space-y-0">
            {cart.map((item) => (
              <div
                key={item.id}
                className="bg-white border-b-2 border-[#1C1C1C] last:border-b-0 shadow-[3px_3px_0px_#1C1C1C] overflow-hidden flex"
              >
              <img
                src={item.image}
                alt={item.name}
                className="w-24 h-24 object-cover flex-shrink-0"
              />
              <div className="flex-1 p-3 flex flex-col justify-between">
                <div>
                  <div className="font-black text-[#1C1C1C] text-sm">
                    {item.name}
                  </div>
                  {item.toppings && item.toppings.length > 0 && (
                    <div className="text-xs text-gray-500 mt-0.5">
                      + {item.toppings.join(", ")}
                    </div>
                  )}
                  {item.note && (
                    <div className="text-xs text-orange-500 mt-0.5">
                      Ghi chú: {item.note}
                    </div>
                  )}
                  <div className="text-xs text-gray-400">
                    Size: {item.size || "M"}
                  </div>
                </div>
                <div className="flex items-center justify-between mt-1">
                  <span className="font-black text-[#FF6B35]">
                    {formatPrice(item.price * item.quantity)}
                  </span>
                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => updateQty(item.id, -1)}
                      className="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-white flex items-center justify-center font-black shadow-[1px_1px_0px_#1C1C1C]"
                    >
                      −
                    </button>
                    <span className="font-black text-[#1C1C1C] text-sm">
                      {item.quantity}
                    </span>
                    <button
                      onClick={() => updateQty(item.id, 1)}
                      className="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C]"
                    >
                      <Plus size={12} />
                    </button>
<button
                      onClick={() => removeItem(item.id)}
                      className="w-7 h-7 rounded-lg border-2 border-red-200 bg-red-50 text-red-500 flex items-center justify-center"
                    >
                      <Trash2 size={12} />
                    </button>
                    <button
                      onClick={() => openEditModal(item.id)}
                      className="w-7 h-7 rounded-lg border-2 border-[#FFD23F] bg-[#FFD23F] text-[#1C1C1C] flex items-center justify-center"
                      title="Sửa/Đổi món"
                    >
                      <Pencil size={12} />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          ))}
          </div>
        </div>

        {/* Upsell */}
        {showUpsell && upsellItem && (
          <div className="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 mb-4 flex items-center gap-3">
            <img
              src={upsellItem.image}
              alt=""
              className="w-12 h-12 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0"
            />
            <div className="flex-1">
              <p className="font-black text-[#1C1C1C] text-xs">
                Thêm {upsellItem.name} chỉ {formatPrice(upsellItem.price)}?
              </p>
              <p className="text-[10px] text-[#1C1C1C]/60">
                Perfect combo với đơn hàng của bạn 😋
              </p>
            </div>
            <div className="flex flex-col gap-1">
              <button
                onClick={handleAddUpsell}
                className="bg-[#FF6B35] text-white text-[10px] font-black px-2 py-1 rounded-lg border border-[#1C1C1C]"
              >
                + Thêm
              </button>
              <button
                onClick={() => setShowUpsell(false)}
                className="text-[10px] text-gray-500"
              >
                Bỏ qua
              </button>
            </div>
          </div>
        )}

        {/* Voucher */}
        <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-4">
          <button
            onClick={() => setShowVouchers(!showVouchers)}
            className="w-full flex items-center justify-between"
          >
            <div className="flex items-center gap-2">
              <Tag size={16} className="text-[#FF6B35]" />
              <span className="font-black text-[#1C1C1C] text-sm">
                {appliedVoucher
                  ? `Đã dùng: ${appliedVoucher.code}`
                  : "Chọn voucher"}
              </span>
            </div>
            {appliedVoucher ? (
              <span className="text-green-600 font-black text-sm">
                -{formatPrice(appliedVoucher.discount)}
              </span>
            ) : (
              <span className="text-gray-400">→</span>
            )}
          </button>

          {/* Voucher input */}
          <div className="mt-3 flex gap-2">
            <input
              value={voucherInput}
              onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                setVoucherInput(e.target.value)
              }
              placeholder="Nhập mã voucher..."
              className="flex-1 border-2 border-[#1C1C1C] rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35]"
              onKeyDown={(e: React.KeyboardEvent<HTMLInputElement>) => {
                if (e.key === "Enter") handleVoucherInput();
              }}
            />
            <button
              onClick={handleVoucherInput}
              className="bg-[#1C1C1C] text-white text-xs font-black px-4 py-2 rounded-xl border-2 border-[#1C1C1C]"
            >
              Áp dụng
            </button>
          </div>

          {showVouchers && (
            <div className="mt-3 space-y-2">
              {VOUCHERS.map((v) => {
                const eligible = subtotal >= v.minOrder;
                return (
                  <div
                    key={v.code}
                    onClick={() => eligible && applyVoucher(v)}
                    className={`flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all ${
                      eligible
                        ? "border-[#FF6B35]/30 hover:border-[#FF6B35] hover:bg-orange-50"
                        : "border-gray-100 opacity-50 cursor-not-allowed"
                    }`}
                  >
                    <div
                      className={`w-2 h-2 rounded-full ${
                        eligible ? "bg-green-500" : "bg-gray-300"
                      }`}
                    />
                    <div className="flex-1">
                      <div className="font-black text-[#1C1C1C] text-sm">
                        {v.code}
                      </div>
                      <div className="text-xs text-gray-500">{v.label}</div>
                      {!eligible && (
                        <div className="text-[10px] text-red-400 mt-0.5">
                          Cần tối thiểu {formatPrice(v.minOrder)}
                        </div>
                      )}
                    </div>
                    {eligible && appliedVoucher?.code === v.code && (
                      <span className="text-green-500 text-xs font-black">
                        Đang dùng ✓
                      </span>
                    )}
                  </div>
                );
              })}
            </div>
          )}
        </div>

        {/* Delivery / Pickup info */}
        {deliveryMethod === "delivery" ? (
          <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-4 space-y-3">
            <div className="flex items-center gap-2">
              <MapPin size={16} className="text-[#FF6B35]" />
              <span className="font-black text-[#1C1C1C] text-sm">
                Địa chỉ giao hàng
              </span>
            </div>
            <input
              defaultValue="123 Nguyễn Huệ, Phường Bến Nghé, Q.1, TP.HCM"
              className="w-full border-2 border-[#1C1C1C] rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35]"
            />
            <div className="flex items-center gap-2">
              <Clock size={16} className="text-[#FF6B35]" />
              <span className="font-black text-[#1C1C1C] text-sm">
                Thờigian giao:{" "}
                <span className="text-gray-500 font-medium">
                  {estimateDeliveryTime(distance)}
                </span>
                <span className="text-[10px] text-gray-400 ml-1">
                  ({distance.toFixed(1)}km)
                </span>
              </span>
            </div>
          </div>
        ) : (
          <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-4 space-y-3">
            <div className="flex items-center gap-2">
              <MapPin size={16} className="text-[#1C1C1C]" />
              <span className="font-black text-[#1C1C1C] text-sm">
                🏪 Đến lấy tại chi nhánh
              </span>
            </div>
            <div className="bg-gray-50 rounded-xl p-3">
              <p className="font-bold text-[#1C1C1C] text-sm">{branch.name}</p>
              <p className="text-xs text-gray-500">{branch.address}</p>
            </div>
            <div className="flex items-center gap-2">
              <Clock size={16} className="text-[#1C1C1C]" />
              <span className="font-black text-[#1C1C1C] text-sm">
                Thờigian chuẩn bị:{" "}
                <span className="text-gray-500 font-medium">~15 phút</span>
              </span>
            </div>
          </div>
        )}

        {/* Payment */}
        <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-4">
          <p className="font-black text-[#1C1C1C] text-sm mb-3 flex items-center gap-2">
            <CreditCard size={16} className="text-[#FF6B35]" /> Thanh toán
          </p>
          <div className="grid grid-cols-2 gap-2">
            {[
              { id: "momo", label: "MoMo", emoji: "💜" },
              { id: "cod", label: "Tiền mặt", emoji: "💵" },
              { id: "zalopay", label: "ZaloPay", emoji: "🔵" },
              { id: "bank", label: "Chuyển khoản", emoji: "🏦" },
            ].map((pm) => (
              <button
                key={pm.id}
                onClick={() => setPaymentMethod(pm.id)}
                className={`flex items-center gap-2 py-2.5 px-3 rounded-xl border-2 text-sm font-bold transition-all ${
                  paymentMethod === pm.id
                    ? "border-[#FF6B35] bg-orange-50 text-[#FF6B35]"
                    : "border-gray-200 text-gray-600 hover:border-gray-300"
                }`}
              >
                <span>{pm.emoji}</span> {pm.label}
              </button>
            ))}
          </div>
        </div>

        {/* Bill summary */}
        <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-4">
          <p className="font-black text-[#1C1C1C] text-sm mb-3">
            Tóm tắt đơn hàng
          </p>
          <div className="space-y-2 text-sm">
            <div className="flex justify-between text-gray-600">
              <span>
                Tạm tính ({cart.reduce((s, i) => s + i.quantity, 0)} món)
              </span>
              <span>{formatPrice(subtotal)}</span>
            </div>
            <div className="flex justify-between text-gray-600">
              <span>Phí vận chuyển</span>
              <span className={shippingFee === 0 ? "text-green-600 font-bold" : ""}>
                {shippingFee === 0 ? "Miễn phí" : formatPrice(shippingFee)}
              </span>
            </div>
            {appliedVoucher && (
              <div className="flex justify-between text-green-600">
                <span>Voucher ({appliedVoucher.code})</span>
                <span>-{formatPrice(discount)}</span>
              </div>
            )}
            <div className="border-t-2 border-[#1C1C1C] pt-2 flex justify-between font-black text-[#1C1C1C] text-base">
              <span>Tổng cộng</span>
              <span className="text-[#FF6B35]">{formatPrice(total)}</span>
            </div>
          </div>
        </div>

        {/* Checkout button */}
        <button
          onClick={handleCheckout}
          className="w-full bg-[#FF6B35] text-white font-black py-4 rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-[2px_2px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all flex items-center justify-center gap-2 text-lg"
        >
          <Zap size={20} /> Đặt hàng ngay · {formatPrice(total)}
        </button>
        <p className="text-center text-xs text-gray-400 mt-2">
          Bằng cách đặt hàng, bạn đồng ý với Điều khoản dịch vụ
        </p>
      </div>

{/* Checkout Modal */}
      {showCheckoutModal && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-6 w-full max-w-sm">
            <div className="text-center mb-4">
              <div className="w-16 h-16 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-full flex items-center justify-center mx-auto mb-3">
                <CheckCircle size={32} className="text-[#1C1C1C]" />
              </div>
              <h3 className="font-black text-[#1C1C1C] text-xl">
                Xác nhận đơn hàng
              </h3>
              <p className="text-gray-500 text-sm mt-1">
                {branch.name} · {distance.toFixed(1)}km
              </p>
            </div>
            <div className="bg-gray-50 rounded-xl p-3 mb-4 space-y-1 text-sm">
              <div className="flex justify-between">
                <span className="text-gray-500">Tạm tính</span>
                <span>{formatPrice(subtotal)}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-500">Phí ship</span>
                <span>{shippingFee === 0 ? "Free" : formatPrice(shippingFee)}</span>
              </div>
              {appliedVoucher && (
                <div className="flex justify-between text-green-600">
                  <span>Voucher</span>
                  <span>-{formatPrice(discount)}</span>
                </div>
              )}
              <div className="border-t border-gray-200 pt-1 flex justify-between font-black">
                <span>Tổng</span>
                <span className="text-[#FF6B35]">{formatPrice(total)}</span>
              </div>
            </div>
            <div className="flex gap-2">
              <button
                onClick={() => setShowCheckoutModal(false)}
                className="flex-1 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-bold text-sm"
              >
                Quay lại
              </button>
              <button
                onClick={confirmCheckout}
                className="flex-1 py-3 rounded-xl bg-[#FF6B35] text-white font-black text-sm border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"
              >
                Xác nhận ✓
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Edit Toppings Modal */}
      {editingItem && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 w-full max-w-sm">
            <div className="flex items-center justify-between mb-3">
              <h3 className="font-black text-[#1C1C1C] text-lg">Sửa/Đổi món</h3>
              <button
                onClick={() => setEditingItem(null)}
                className="w-8 h-8 rounded-full border-2 border-gray-200 flex items-center justify-center"
              >
                <X size={16} />
              </button>
            </div>
            <p className="text-xs text-gray-500 mb-3">Chọn thêm topping:</p>
            <div className="space-y-2 max-h-60 overflow-y-auto">
              {AVAILABLE_TOPPINGS.map((topping) => (
                <div
                  key={topping.id}
                  onClick={() => toggleTopping(topping.id)}
                  className={`flex items-center justify-between p-3 rounded-xl border-2 cursor-pointer transition-all ${
                    editingToppings.includes(topping.id)
                      ? "border-[#FF6B35] bg-orange-50"
                      : "border-gray-200 hover:border-gray-300"
                  }`}
                >
                  <div className="flex items-center gap-2">
                    <div
                      className={`w-5 h-5 rounded-full border-2 flex items-center justify-center ${
                        editingToppings.includes(topping.id)
                          ? "bg-[#FF6B35] border-[#FF6B35]"
                          : "border-gray-300"
                      }`}
                    >
                      {editingToppings.includes(topping.id) && (
                        <CheckCircle size={12} className="text-white" />
                      )}
                    </div>
                    <span className="font-bold text-sm text-[#1C1C1C]">
                      {topping.name}
                    </span>
                  </div>
                  <span className="font-black text-[#FF6B35] text-sm">
                    +{formatPrice(topping.price)}
                  </span>
                </div>
              ))}
            </div>
            <div className="flex gap-2 mt-4">
              <button
                onClick={() => setEditingItem(null)}
                className="flex-1 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-bold text-sm"
              >
                Hủy
              </button>
              <button
                onClick={saveToppings}
                className="flex-1 py-3 rounded-xl bg-[#FF6B35] text-white font-black text-sm border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"
              >
                Lưu ✓
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

