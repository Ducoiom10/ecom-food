import { useState } from "react";
import { useNavigate } from "react-router";
import { Trash2, Tag, MapPin, Clock, CreditCard, Banknote, ChevronRight, Zap, Plus } from "lucide-react";
import { MENU_ITEMS, formatPrice } from "../../data/mockData";

const SAMPLE_CART = [
  { id: "1", menuItemId: "1", quantity: 2, size: "M", toppings: ["Trứng lòng đào"], note: "Ít hành" },
  { id: "2", menuItemId: "2", quantity: 1, size: "L", toppings: [], note: "" },
];

const VOUCHERS = [
  { code: "LUNCH15K", label: "Giảm 15.000đ cho đơn từ 80k", discount: 15000, minOrder: 80000, type: "flat" },
  { code: "BANANH20", label: "Giảm 20% tối đa 30k", discount: 30000, minOrder: 50000, type: "percent" },
  { code: "FREESHIP", label: "Miễn phí vận chuyển", discount: 15000, minOrder: 0, type: "ship" },
];

export function CartPage() {
  const navigate = useNavigate();
  const [cart, setCart] = useState(SAMPLE_CART);
  const [appliedVoucher, setAppliedVoucher] = useState<(typeof VOUCHERS)[0] | null>(null);
  const [showVouchers, setShowVouchers] = useState(false);
  const [voucherInput, setVoucherInput] = useState("");
  const [deliveryMethod, setDeliveryMethod] = useState<"delivery" | "pickup">("delivery");
  const [paymentMethod, setPaymentMethod] = useState("momo");
  const [showUpsell, setShowUpsell] = useState(true);

  const subtotal = cart.reduce((sum, item) => {
    const menu = MENU_ITEMS.find((m) => m.id === item.menuItemId);
    return sum + (menu?.price ?? 0) * item.quantity;
  }, 0);

  const shippingFee = deliveryMethod === "pickup" ? 0 : 15000;
  const discount = appliedVoucher?.discount ?? 0;
  const total = subtotal + shippingFee - discount;

  const updateQty = (id: string, delta: number) => {
    setCart((prev) => prev.map((item) => item.id === id ? { ...item, quantity: Math.max(1, item.quantity + delta) } : item));
  };

  const removeItem = (id: string) => {
    setCart((prev) => prev.filter((item) => item.id !== id));
  };

  const applyVoucher = (voucher: typeof VOUCHERS[0]) => {
    if (subtotal >= voucher.minOrder) {
      setAppliedVoucher(voucher);
      setShowVouchers(false);
    }
  };

  const freeShippingTarget = 100000;
  const remainingForFreeShip = Math.max(0, freeShippingTarget - subtotal);
  const shipProgress = Math.min(100, (subtotal / freeShippingTarget) * 100);

  return (
    <div className="pb-4">
      <div className="px-4 pt-4">
        <h1 className="font-black text-[#1C1C1C] text-xl mb-4">Giỏ hàng của bạn 🛒</h1>

        {/* Free shipping progress */}
        {remainingForFreeShip > 0 && deliveryMethod === "delivery" && (
          <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 mb-4">
            <div className="flex items-center gap-2 mb-2">
              <span className="text-lg">🛵</span>
              <p className="text-xs font-bold text-[#1C1C1C]">
                Mua thêm <span className="text-[#FF6B35] font-black">{formatPrice(remainingForFreeShip)}</span> để được <span className="text-green-600 font-black">miễn phí vận chuyển!</span>
              </p>
            </div>
            <div className="relative h-3 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
              <div className="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full transition-all" style={{ width: `${shipProgress}%` }} />
              <div className="absolute right-0 top-1/2 -translate-y-1/2 text-base" style={{ left: `${shipProgress}%`, transform: "translate(-50%, -50%)" }}>🛵</div>
            </div>
          </div>
        )}

        {/* Delivery mode */}
        <div className="flex gap-2 mb-4">
          <button onClick={() => setDeliveryMethod("delivery")} className={`flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold transition-all ${deliveryMethod === "delivery" ? "bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]" : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"}`}>
            🛵 Giao hàng (+{formatPrice(shippingFee)})
          </button>
          <button onClick={() => setDeliveryMethod("pickup")} className={`flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold transition-all ${deliveryMethod === "pickup" ? "bg-[#1C1C1C] text-white shadow-[2px_2px_0px_#FF6B35]" : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"}`}>
            🏪 Tự đến lấy (Free)
          </button>
        </div>

        {/* Cart items */}
        <div className="space-y-3 mb-4">
          {cart.map((item) => {
            const menu = MENU_ITEMS.find((m) => m.id === item.menuItemId);
            if (!menu) return null;
            return (
              <div key={item.id} className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden flex">
                <img src={menu.image} alt={menu.name} className="w-24 h-24 object-cover flex-shrink-0" />
                <div className="flex-1 p-3 flex flex-col justify-between">
                  <div>
                    <div className="font-black text-[#1C1C1C] text-sm">{menu.name}</div>
                    {item.toppings.length > 0 && <div className="text-xs text-gray-500 mt-0.5">+ {item.toppings.join(", ")}</div>}
                    {item.note && <div className="text-xs text-orange-500 mt-0.5">Ghi chú: {item.note}</div>}
                    <div className="text-xs text-gray-400">Size: {item.size}</div>
                  </div>
                  <div className="flex items-center justify-between mt-1">
                    <span className="font-black text-[#FF6B35]">{formatPrice(menu.price * item.quantity)}</span>
                    <div className="flex items-center gap-2">
                      <button onClick={() => updateQty(item.id, -1)} className="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-white flex items-center justify-center font-black shadow-[1px_1px_0px_#1C1C1C]">−</button>
                      <span className="font-black text-[#1C1C1C] text-sm">{item.quantity}</span>
                      <button onClick={() => updateQty(item.id, 1)} className="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C]"><Plus size={12} /></button>
                      <button onClick={() => removeItem(item.id)} className="w-7 h-7 rounded-lg border-2 border-red-200 bg-red-50 text-red-500 flex items-center justify-center"><Trash2 size={12} /></button>
                    </div>
                  </div>
                </div>
              </div>
            );
          })}
        </div>

        {/* Upsell */}
        {showUpsell && (
          <div className="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 mb-4 flex items-center gap-3">
            <img src={MENU_ITEMS.find((m) => m.id === "8")?.image} alt="" className="w-12 h-12 object-cover rounded-xl border-2 border-[#1C1C1C] flex-shrink-0" />
            <div className="flex-1">
              <p className="font-black text-[#1C1C1C] text-xs">Thêm Chả giò giòn chỉ 25.000đ?</p>
              <p className="text-[10px] text-[#1C1C1C]/60">Perfect combo với mì trộn của bạn 😋</p>
            </div>
            <div className="flex flex-col gap-1">
              <button className="bg-[#FF6B35] text-white text-[10px] font-black px-2 py-1 rounded-lg border border-[#1C1C1C]">+ Thêm</button>
              <button onClick={() => setShowUpsell(false)} className="text-[10px] text-gray-500">Bỏ qua</button>
            </div>
          </div>
        )}

        {/* Voucher */}
        <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-4">
          <button onClick={() => setShowVouchers(!showVouchers)} className="w-full flex items-center justify-between">
            <div className="flex items-center gap-2">
              <Tag size={16} className="text-[#FF6B35]" />
              <span className="font-black text-[#1C1C1C] text-sm">{appliedVoucher ? `Đã dùng: ${appliedVoucher.code}` : "Chọn voucher"}</span>
            </div>
            {appliedVoucher ? (
              <span className="text-green-600 font-black text-sm">-{formatPrice(appliedVoucher.discount)}</span>
            ) : (
              <ChevronRight size={16} className="text-gray-400" />
            )}
          </button>
          {showVouchers && (
            <div className="mt-3 space-y-2">
              {VOUCHERS.map((v) => {
                const eligible = subtotal >= v.minOrder;
                return (
                  <div
                    key={v.code}
                    onClick={() => eligible && applyVoucher(v)}
                    className={`flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all ${eligible ? "border-[#FF6B35]/30 hover:border-[#FF6B35] hover:bg-orange-50" : "border-gray-100 opacity-50 cursor-not-allowed"}`}
                  >
                    <div className={`w-2 h-2 rounded-full ${eligible ? "bg-green-500" : "bg-gray-300"}`} />
                    <div className="flex-1">
                      <div className="font-black text-[#1C1C1C] text-sm">{v.code}</div>
                      <div className="text-xs text-gray-500">{v.label}</div>
                      {!eligible && <div className="text-[10px] text-red-400 mt-0.5">Cần tối thiểu {formatPrice(v.minOrder)}</div>}
                    </div>
                    {eligible && appliedVoucher?.code === v.code && (
                      <span className="text-green-500 text-xs font-black">Đang dùng ✓</span>
                    )}
                  </div>
                );
              })}
            </div>
          )}
        </div>

        {/* Delivery info */}
        {deliveryMethod === "delivery" && (
          <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-4 space-y-3">
            <div className="flex items-center gap-2">
              <MapPin size={16} className="text-[#FF6B35]" />
              <span className="font-black text-[#1C1C1C] text-sm">Địa chỉ giao hàng</span>
            </div>
            <input defaultValue="123 Nguyễn Huệ, Phường Bến Nghé, Q.1, TP.HCM" className="w-full border-2 border-[#1C1C1C] rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35]" />
            <div className="flex items-center gap-2">
              <Clock size={16} className="text-[#FF6B35]" />
              <span className="font-black text-[#1C1C1C] text-sm">Thời gian giao: <span className="text-gray-500 font-medium">~20-25 phút</span></span>
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
                className={`flex items-center gap-2 py-2.5 px-3 rounded-xl border-2 text-sm font-bold transition-all ${paymentMethod === pm.id ? "border-[#FF6B35] bg-orange-50 text-[#FF6B35]" : "border-gray-200 text-gray-600 hover:border-gray-300"}`}
              >
                <span>{pm.emoji}</span> {pm.label}
              </button>
            ))}
          </div>
        </div>

        {/* Bill summary */}
        <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 mb-4">
          <p className="font-black text-[#1C1C1C] text-sm mb-3">Tóm tắt đơn hàng</p>
          <div className="space-y-2 text-sm">
            <div className="flex justify-between text-gray-600">
              <span>Tạm tính ({cart.reduce((s, i) => s + i.quantity, 0)} món)</span>
              <span>{formatPrice(subtotal)}</span>
            </div>
            <div className="flex justify-between text-gray-600">
              <span>Phí vận chuyển</span>
              <span className={shippingFee === 0 ? "text-green-600 font-bold" : ""}>{shippingFee === 0 ? "Miễn phí" : formatPrice(shippingFee)}</span>
            </div>
            {appliedVoucher && (
              <div className="flex justify-between text-green-600">
                <span>Voucher ({appliedVoucher.code})</span>
                <span>-{formatPrice(appliedVoucher.discount)}</span>
              </div>
            )}
            <div className="border-t-2 border-[#1C1C1C] pt-2 flex justify-between font-black text-[#1C1C1C] text-base">
              <span>Tổng cộng</span>
              <span className="text-[#FF6B35]">{formatPrice(total)}</span>
            </div>
          </div>
        </div>

        {/* Checkout button */}
        <button className="w-full bg-[#FF6B35] text-white font-black py-4 rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-[2px_2px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all flex items-center justify-center gap-2 text-lg">
          <Zap size={20} /> Đặt hàng ngay · {formatPrice(total)}
        </button>
        <p className="text-center text-xs text-gray-400 mt-2">Bằng cách đặt hàng, bạn đồng ý với Điều khoản dịch vụ</p>
      </div>
    </div>
  );
}
