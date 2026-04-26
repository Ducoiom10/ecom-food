import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router";
import { CheckCircle, Copy, Check, Smartphone, Banknote, CreditCard, Share2, Users, ChevronDown, ChevronUp } from "lucide-react";
import { MENU_ITEMS, formatPrice } from "../../data/mockData";

interface BillItem {
  participantId: string;
  participantName: string;
  emoji: string;
  items: { name: string; quantity: number; price: number; subtotal: number }[];
  total: number;
  isPaid: boolean;
}

const PAYMENT_METHODS = [
  { id: "momo", label: "MoMo", emoji: "💜", color: "bg-purple-500" },
  { id: "bank", label: "Chuyển khoản", emoji: "🏦", color: "bg-blue-500" },
  { id: "cod", label: "Tiền mặt", emoji: "💵", color: "bg-green-500" },
  { id: "zalopay", label: "ZaloPay", emoji: "🔵", color: "bg-blue-400" },
];

export function SplitBill() {
  const { roomId } = useParams<{ roomId: string }>();
  const navigate = useNavigate();
  const [bills, setBills] = useState<BillItem[]>([]);
  const [grandTotal, setGrandTotal] = useState(0);
  const [copiedId, setCopiedId] = useState<string | null>(null);
  const [expandedId, setExpandedId] = useState<string | null>(null);
  const [confirmedPayment, setConfirmedPayment] = useState<Set<string>>(new Set());
  const [selectedPayment, setSelectedPayment] = useState("momo");
  const [showConfetti, setShowConfetti] = useState(false);

  useEffect(() => {
    const raw = localStorage.getItem(`room_${roomId}`);
    if (!raw) return;
    const room = JSON.parse(raw);

    const billData: BillItem[] = room.participants.map((p: any) => {
      const order = room.orders.find((o: any) => o.participantId === p.id);
      const items = (order?.items ?? []).map((it: any) => {
        const menu = MENU_ITEMS.find((m) => m.id === it.menuItemId);
        return {
          name: menu?.name ?? it.menuItemId,
          quantity: it.quantity,
          price: menu?.price ?? 0,
          subtotal: (menu?.price ?? 0) * it.quantity,
        };
      });
      const total = items.reduce((sum: number, it: any) => sum + it.subtotal, 0);
      return { participantId: p.id, participantName: p.name, emoji: p.emoji, items, total, isPaid: false };
    });

    setBills(billData);
    setGrandTotal(billData.reduce((sum, b) => sum + b.total, 0));
  }, [roomId]);

  const paidCount = confirmedPayment.size;
  const allPaid = bills.length > 0 && paidCount === bills.length;

  useEffect(() => {
    if (allPaid) setShowConfetti(true);
  }, [allPaid]);

  const handleCopyBill = (bill: BillItem) => {
    const text = [
      `🧾 Bill của ${bill.participantName} - Phòng ${roomId}`,
      "",
      ...bill.items.map((it) => `• ${it.name} x${it.quantity} = ${formatPrice(it.subtotal)}`),
      "",
      `💰 Tổng: ${formatPrice(bill.total)}`,
      "",
      `Chuyển khoản qua MoMo: 0901234567 (Ba Anh Em)`,
      `Nội dung: ${bill.participantName} thanh toán phòng ${roomId}`,
    ].join("\n");
    navigator.clipboard.writeText(text);
    setCopiedId(bill.participantId);
    setTimeout(() => setCopiedId(null), 2000);
  };

  const handleMarkPaid = (participantId: string) => {
    setConfirmedPayment((prev) => {
      const next = new Set(prev);
      if (next.has(participantId)) next.delete(participantId);
      else next.add(participantId);
      return next;
    });
  };

  return (
    <div className="min-h-screen bg-[#FAFAF8] pb-8">
      {/* Confetti effect */}
      {showConfetti && (
        <div className="fixed inset-0 pointer-events-none z-50 flex items-center justify-center">
          <div className="text-center animate-bounce">
            <div className="text-6xl mb-2">🎉</div>
            <div className="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] px-6 py-3">
              <p className="font-black text-[#1C1C1C] text-lg">Tất cả đã thanh toán!</p>
              <p className="text-sm text-gray-600">Enjoy bữa ăn! 😋</p>
            </div>
          </div>
        </div>
      )}

      {/* Header */}
      <div className="bg-[#1C1C1C] px-4 pt-8 pb-6 border-b-2 border-[#1C1C1C]">
        <div className="flex items-center gap-3 mb-4">
          <div className="w-12 h-12 bg-[#FFD23F] border-2 border-[#FFD23F] rounded-2xl flex items-center justify-center">
            <Users size={24} className="text-[#1C1C1C]" />
          </div>
          <div>
            <h1 className="text-white font-black text-xl">Chia Bill Nhóm</h1>
            <p className="text-gray-400 text-xs">Phòng #{roomId} · {bills.length} người</p>
          </div>
        </div>

        {/* Summary */}
        <div className="grid grid-cols-3 gap-3">
          <div className="bg-white/10 rounded-xl p-3 text-center">
            <div className="text-[#FFD23F] font-black text-lg">{formatPrice(grandTotal)}</div>
            <div className="text-white/60 text-[10px]">Tổng đơn</div>
          </div>
          <div className="bg-white/10 rounded-xl p-3 text-center">
            <div className="text-green-400 font-black text-lg">{paidCount}/{bills.length}</div>
            <div className="text-white/60 text-[10px]">Đã trả</div>
          </div>
          <div className="bg-white/10 rounded-xl p-3 text-center">
            <div className="text-[#FF6B35] font-black text-lg">
              {formatPrice(bills.filter((b) => !confirmedPayment.has(b.participantId)).reduce((sum, b) => sum + b.total, 0))}
            </div>
            <div className="text-white/60 text-[10px]">Còn lại</div>
          </div>
        </div>

        {/* Progress */}
        <div className="mt-4">
          <div className="flex justify-between text-xs text-gray-400 mb-1">
            <span>Tiến độ thanh toán</span>
            <span>{bills.length > 0 ? Math.round((paidCount / bills.length) * 100) : 0}%</span>
          </div>
          <div className="h-3 bg-white/20 rounded-full overflow-hidden border border-white/30">
            <div
              className="h-full bg-gradient-to-r from-[#FFD23F] to-[#FF6B35] rounded-full transition-all duration-500"
              style={{ width: `${bills.length > 0 ? (paidCount / bills.length) * 100 : 0}%` }}
            />
          </div>
        </div>
      </div>

      {/* Payment method selector */}
      <div className="px-4 mt-4">
        <p className="text-xs font-black text-[#1C1C1C] mb-2 uppercase tracking-wide">Phương thức thanh toán</p>
        <div className="flex gap-2">
          {PAYMENT_METHODS.map((pm) => (
            <button
              key={pm.id}
              onClick={() => setSelectedPayment(pm.id)}
              className={`flex-1 flex flex-col items-center py-2 rounded-xl border-2 border-[#1C1C1C] text-xs font-bold transition-all ${selectedPayment === pm.id ? "bg-[#1C1C1C] text-white shadow-[2px_2px_0px_#FF6B35]" : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"}`}
            >
              <span className="text-lg">{pm.emoji}</span>
              <span className="text-[9px]">{pm.label}</span>
            </button>
          ))}
        </div>
      </div>

      {/* Individual bills */}
      <div className="px-4 mt-4 space-y-3">
        <h2 className="font-black text-[#1C1C1C] flex items-center gap-2">
          🧾 Bill từng người
        </h2>
        {bills.map((bill) => {
          const isPaid = confirmedPayment.has(bill.participantId);
          const isExpanded = expandedId === bill.participantId;
          return (
            <div
              key={bill.participantId}
              className={`bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden transition-all ${isPaid ? "opacity-70" : ""}`}
            >
              {/* Bill header */}
              <div className="p-4">
                <div className="flex items-center gap-3 mb-3">
                  <div className="w-10 h-10 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                    {isPaid ? "✅" : bill.emoji}
                  </div>
                  <div className="flex-1">
                    <div className="font-black text-[#1C1C1C] flex items-center gap-2">
                      {bill.participantName}
                      {isPaid && <span className="text-green-500 text-xs">(Đã trả)</span>}
                    </div>
                    <div className="text-xs text-gray-400">{bill.items.length} món · {bill.items.reduce((s, i) => s + i.quantity, 0)} phần</div>
                  </div>
                  <div className="font-black text-[#FF6B35] text-lg">{formatPrice(bill.total)}</div>
                </div>

                {/* Item list toggle */}
                <button
                  onClick={() => setExpandedId(isExpanded ? null : bill.participantId)}
                  className="w-full flex items-center justify-between text-xs text-gray-500 bg-gray-50 rounded-xl px-3 py-2 mb-3 border border-gray-200"
                >
                  <span>Chi tiết đơn hàng</span>
                  {isExpanded ? <ChevronUp size={14} /> : <ChevronDown size={14} />}
                </button>

                {isExpanded && (
                  <div className="space-y-1.5 mb-3 bg-gray-50 rounded-xl p-3">
                    {bill.items.length === 0 ? (
                      <p className="text-xs text-gray-400 italic text-center py-2">Không có món nào</p>
                    ) : (
                      bill.items.map((it, idx) => (
                        <div key={idx} className="flex items-center justify-between text-xs">
                          <span className="text-gray-700">{it.name} <span className="text-gray-400">x{it.quantity}</span></span>
                          <span className="font-bold text-[#1C1C1C]">{formatPrice(it.subtotal)}</span>
                        </div>
                      ))
                    )}
                    <div className="border-t border-gray-200 pt-1.5 mt-1.5 flex justify-between text-xs font-black">
                      <span>Tổng</span>
                      <span className="text-[#FF6B35]">{formatPrice(bill.total)}</span>
                    </div>
                  </div>
                )}

                {/* Payment info */}
                {selectedPayment === "momo" && !isPaid && bill.total > 0 && (
                  <div className="bg-purple-50 border border-purple-200 rounded-xl p-3 mb-3 text-xs">
                    <div className="font-bold text-purple-700 mb-1">Chuyển MoMo cho host:</div>
                    <div className="text-gray-600">SĐT: <span className="font-bold text-[#1C1C1C]">0901 234 567</span></div>
                    <div className="text-gray-600">Nội dung: <span className="font-bold text-[#1C1C1C]">{bill.participantName} {roomId}</span></div>
                    <div className="text-gray-600">Số tiền: <span className="font-bold text-purple-600 text-sm">{formatPrice(bill.total)}</span></div>
                  </div>
                )}

                {/* Actions */}
                {bill.total > 0 && (
                  <div className="flex gap-2">
                    <button
                      onClick={() => handleCopyBill(bill)}
                      className="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl border-2 border-[#1C1C1C] bg-[#FFD23F] text-[#1C1C1C] text-xs font-black shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] transition-all"
                    >
                      {copiedId === bill.participantId ? <Check size={14} /> : <Copy size={14} />}
                      {copiedId === bill.participantId ? "Đã copy!" : "Copy bill"}
                    </button>
                    <button
                      onClick={() => handleMarkPaid(bill.participantId)}
                      className={`flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-xs font-black shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] transition-all ${isPaid ? "bg-gray-200 text-gray-600" : "bg-green-500 text-white"}`}
                    >
                      <CheckCircle size={14} />
                      {isPaid ? "Bỏ xác nhận" : "Đã trả ✓"}
                    </button>
                  </div>
                )}
                {bill.total === 0 && (
                  <div className="text-center text-gray-400 text-xs py-2">Không đặt món nào</div>
                )}
              </div>
            </div>
          );
        })}
      </div>

      {/* Share button */}
      <div className="px-4 mt-4">
        <button
          onClick={() => {
            navigator.clipboard.writeText(`${window.location.href}`);
          }}
          className="w-full flex items-center justify-center gap-2 py-3.5 bg-[#1C1C1C] text-white font-black rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#FF6B35] hover:shadow-[2px_2px_0px_#FF6B35] transition-all"
        >
          <Share2 size={18} /> Chia sẻ bill cho cả nhóm
        </button>
        <button
          onClick={() => navigate("/")}
          className="w-full mt-2 text-center text-sm text-[#FF6B35] font-bold py-2"
        >
          Đặt đơn mới →
        </button>
      </div>
    </div>
  );
}
