import { useState } from "react";
import {
  Star,
  Clock,
  RotateCcw,
  ChevronRight,
  Award,
  Bell,
  Shield,
  HelpCircle,
  LogOut,
  TrendingUp,
} from "lucide-react";
import { formatPrice } from "../../data/mockData";
import { useCartContext } from "../../context/CartContext";
import { toast } from "sonner";

const ORDER_HISTORY = [
  {
    id: "ORD-198",
    date: "20/04/2026",
    items: [
      { menuItemId: "1", name: "Mì trộn đặc biệt", qty: 2, price: 45000 },
      { menuItemId: "2", name: "Trà sữa trân châu đen", qty: 1, price: 35000 },
    ],
    total: 125000,
    status: "completed",
  },
  {
    id: "ORD-185",
    date: "18/04/2026",
    items: [
      { menuItemId: "6", name: "Phở bò đặc biệt", qty: 1, price: 65000 },
      { menuItemId: "5", name: "Sinh tố xoài nhiệt đới", qty: 1, price: 40000 },
    ],
    total: 105000,
    status: "completed",
  },
  {
    id: "ORD-171",
    date: "15/04/2026",
    items: [
      { menuItemId: "1", name: "Mì trộn đặc biệt", qty: 3, price: 45000 },
    ],
    total: 195000,
    status: "completed",
  },
];

export function ProfilePage() {
  const [snackPoints] = useState(342);
  const [tab, setTab] = useState<"orders" | "loyalty">("orders");
  const { addItem } = useCartContext();

  const handleReorder = (order: (typeof ORDER_HISTORY)[0]) => {
    order.items.forEach((item) => {
      addItem({
        menuItemId: item.menuItemId,
        name: item.name,
        image: "",
        price: item.price,
        quantity: item.qty,
      });
    });
    toast.success(`Đã thêm ${order.items.length} món vào giỏ hàng`);
  };

  const handleSettingClick = (label: string) => {
    toast.info(`Tính năng "${label}" sẽ ra mắt sớm!`, { icon: "🔔" });
  };

  return (
    <div className="pb-4">
      {/* Profile header */}
      <div className="bg-[#1C1C1C] px-4 pt-6 pb-8 relative overflow-hidden">
        <div className="absolute top-0 right-0 w-32 h-32 bg-[#FF6B35]/20 rounded-full -translate-x-4 -translate-y-4" />
        <div className="relative z-10 flex items-center gap-4">
          <div className="w-16 h-16 bg-[#FF6B35] border-2 border-[#FFD23F] rounded-2xl flex items-center justify-center text-2xl shadow-[4px_4px_0px_#FFD23F]">
            👤
          </div>
          <div>
            <h2 className="text-white font-black text-xl">Minh Tuấn</h2>
            <p className="text-gray-400 text-sm">minhtuan@email.com</p>
            <div className="flex items-center gap-2 mt-1">
              <div className="bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-2 py-0.5 rounded-full flex items-center gap-1">
                <Award size={10} /> VIP Gold
              </div>
              <span className="text-gray-500 text-xs">
                Thành viên từ 01/2026
              </span>
            </div>
          </div>
        </div>
      </div>

      {/* Snack Points */}
      <div className="mx-4 -mt-4 relative z-10">
        <div className="bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 flex items-center gap-4">
          <div className="w-12 h-12 bg-[#1C1C1C] rounded-xl flex items-center justify-center flex-shrink-0">
            <Star size={24} className="text-[#FFD23F]" />
          </div>
          <div className="flex-1">
            <p className="text-[#1C1C1C]/60 text-xs font-bold uppercase">
              Snack Points
            </p>
            <p className="font-black text-[#1C1C1C] text-3xl">
              {snackPoints.toLocaleString()}
            </p>
            <p className="text-[#1C1C1C]/70 text-xs mt-0.5">
              ≈ {formatPrice(snackPoints * 100)} · Đủ dùng 1 bữa miễn phí!
            </p>
          </div>
          <div>
            <TrendingUp size={20} className="text-[#1C1C1C]" />
            <span className="text-xs font-black text-green-700">+42 tuần này</span>
          </div>
        </div>
      </div>

      {/* Tabs */}
      <div className="flex px-4 mt-4 gap-2">
        {(["orders", "loyalty"] as const).map((t) => (
          <button
            key={t}
            onClick={() => setTab(t)}
            className={`flex-1 py-2.5 rounded-xl border-2 border-[#1C1C1C] text-sm font-black transition-all ${
              tab === t
                ? "bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]"
                : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"
            }`}
          >
            {t === "orders" ? "🧾 Lịch sử đơn" : "⭐ Phần thưởng"}
          </button>
        ))}
      </div>

      {tab === "orders" && (
        <div className="px-4 mt-4 space-y-3">
          {ORDER_HISTORY.map((order) => (
            <div
              key={order.id}
              className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4"
            >
              <div className="flex items-center justify-between mb-2">
                <div>
                  <span className="font-black text-[#1C1C1C] text-sm">
                    {order.id}
                  </span>
                  <div className="flex items-center gap-1 text-gray-400 text-xs mt-0.5">
                    <Clock size={10} />
                    <span>{order.date}</span>
                  </div>
                </div>
                <span className="bg-green-100 text-green-600 text-xs font-black px-2 py-0.5 rounded-full border border-green-200">
                  ✓ Hoàn thành
                </span>
              </div>
              <div className="text-xs text-gray-500 mb-3">
                {order.items.map((i) => `${i.name} x${i.qty}`).join(" · ")}
              </div>
              <div className="flex items-center justify-between">
                <span className="font-black text-[#FF6B35]">
                  {formatPrice(order.total)}
                </span>
                <button
                  onClick={() => handleReorder(order)}
                  className="flex items-center gap-1.5 bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-3 py-2 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] transition-all"
                >
                  <RotateCcw size={12} /> Đặt lại
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {tab === "loyalty" && (
        <div className="px-4 mt-4 space-y-3">
          {[
            {
              title: "Mua 5 đơn liên tiếp",
              points: 50,
              progress: 3,
              max: 5,
              emoji: "🎯",
            },
            {
              title: "Đặt vào giờ trưa tuần này",
              points: 30,
              progress: 4,
              max: 5,
              emoji: "☀️",
            },
            {
              title: "Thử thực đơn mới",
              points: 20,
              progress: 1,
              max: 1,
              emoji: "✨",
              completed: true,
            },
            {
              title: "Giới thiệu bạn bè",
              points: 100,
              progress: 0,
              max: 1,
              emoji: "👥",
            },
          ].map((challenge, i) => (
            <div
              key={i}
              className={`bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 ${
                challenge.completed ? "opacity-70" : ""
              }`}
            >
              <div className="flex items-center gap-3">
                <span className="text-2xl">{challenge.emoji}</span>
                <div className="flex-1">
                  <div className="flex items-center justify-between">
                    <span className="font-black text-[#1C1C1C] text-sm">
                      {challenge.title}
                    </span>
                    <span className="text-[#FFD23F] font-black text-sm">
                      +{challenge.points} pts
                    </span>
                  </div>
                  <div className="flex items-center gap-2 mt-1.5">
                    <div className="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                      <div
                        className="h-full bg-[#FF6B35] rounded-full"
                        style={{
                          width: `${(challenge.progress / challenge.max) * 100}%`,
                        }}
                      />
                    </div>
                    <span className="text-xs text-gray-500">
                      {challenge.progress}/{challenge.max}
                    </span>
                  </div>
                </div>
              </div>
              {challenge.completed && (
                <div className="mt-2 bg-green-50 border border-green-200 rounded-xl px-3 py-1.5 text-xs text-green-600 font-bold text-center">
                  ✓ Đã hoàn thành!
                </div>
              )}
            </div>
          ))}
        </div>
      )}

      {/* Settings */}
      <div className="px-4 mt-6 space-y-2">
        {[
          {
            icon: Bell,
            label: "Thông báo",
            sub: "Email & Push notification",
          },
          { icon: Shield, label: "Bảo mật", sub: "Đổi mật khẩu, 2FA" },
          { icon: HelpCircle, label: "Trợ giúp", sub: "FAQ, Báo cáo sự cố" },
        ].map(({ icon: Icon, label, sub }) => (
          <button
            key={label}
            onClick={() => handleSettingClick(label)}
            className="w-full bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] px-4 py-3 flex items-center gap-3 hover:shadow-[1px_1px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all"
          >
            <div className="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
              <Icon size={18} className="text-[#1C1C1C]" />
            </div>
            <div className="flex-1 text-left">
              <div className="font-black text-[#1C1C1C] text-sm">{label}</div>
              <div className="text-xs text-gray-400">{sub}</div>
            </div>
            <ChevronRight size={16} className="text-gray-400" />
          </button>
        ))}
        <button className="w-full bg-red-50 border-2 border-red-200 rounded-2xl px-4 py-3 flex items-center gap-3 hover:bg-red-100 transition-all">
          <div className="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <LogOut size={18} className="text-red-500" />
          </div>
          <span className="font-black text-red-500 text-sm">Đăng xuất</span>
        </button>
      </div>
    </div>
  );
}

