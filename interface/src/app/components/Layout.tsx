import { useState } from "react";
import { Outlet, useNavigate, useLocation } from "react-router";
import {
  Home,
  UtensilsCrossed,
  ShoppingCart,
  User,
  Search,
  MapPin,
  ChevronDown,
  Bell,
} from "lucide-react";
import { useBranch, BRANCHES } from "../hooks/useBranch";
import { useCartContext } from "../context/CartContext";
import { toast } from "sonner";

// Mock notifications
const NOTIFICATIONS = [
  { id: 1, text: "🔥 Flash Sale giờ trưa - Giảm 20%", read: false },
  { id: 2, text: "🚚 Đơn hàng #ORD-198 đã được giao", read: false },
  { id: 3, text: "🎁 Bạn nhận được voucher LUNCH15K", read: true },
];

export function Layout() {
  const navigate = useNavigate();
  const location = useLocation();
  const { branch, distance, selectBranch } = useBranch();
  const { totalItems } = useCartContext();

  const [showBranchMenu, setShowBranchMenu] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [showNotif, setShowNotif] = useState(false);
  const [notifs, setNotifs] = useState(NOTIFICATIONS);

  const navItems = [
    { path: "/", icon: Home, label: "Trang chủ" },
    { path: "/menu", icon: UtensilsCrossed, label: "Thực đơn" },
    { path: "/cart", icon: ShoppingCart, label: "Giỏ hàng" },
    { path: "/profile", icon: User, label: "Tài khoản" },
  ];

  const isActive = (path: string) => {
    if (path === "/") return location.pathname === "/";
    return location.pathname.startsWith(path);
  };

  const handleSearch = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === "Enter" && searchQuery.trim()) {
      navigate(`/menu?search=${encodeURIComponent(searchQuery.trim())}`);
      setSearchQuery("");
    }
  };

  const handleSearchClick = () => {
    if (searchQuery.trim()) {
      navigate(`/menu?search=${encodeURIComponent(searchQuery.trim())}`);
      setSearchQuery("");
    }
  };

  const unreadCount = notifs.filter((n: (typeof NOTIFICATIONS)[0]) => !n.read).length;

  const markRead = (id: number) => {
    setNotifs((prev: typeof NOTIFICATIONS) =>
      prev.map((n: (typeof NOTIFICATIONS)[0]) => (n.id === id ? { ...n, read: true } : n))
    );
  };

  return (
    <div className="min-h-screen bg-[#FAFAF8] flex flex-col max-w-[430px] mx-auto relative">
      {/* Header */}
      <header className="sticky top-0 z-40 bg-[#FAFAF8] border-b-2 border-[#1C1C1C] px-4 pt-3 pb-3">
        {/* Top row */}
        <div className="flex items-center justify-between mb-3">
          {/* Logo */}
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 bg-[#FF6B35] border-2 border-[#1C1C1C] rounded-lg flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C]">
              <span className="text-white text-xs">🍜</span>
            </div>
            <span className="font-black text-[#1C1C1C] tracking-tight">Ba Anh Em</span>
          </div>

          {/* Branch selector */}
          <div className="relative">
            <button
              onClick={() => setShowBranchMenu(!showBranchMenu)}
              className="flex items-center gap-1 text-xs border-2 border-[#1C1C1C] rounded-lg px-2 py-1 bg-white shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all"
            >
              <MapPin size={12} className="text-[#FF6B35]" />
              <span className="max-w-[100px] truncate font-medium">
                {branch.name.replace("Chi nhánh ", "")}
              </span>
              <ChevronDown size={12} />
            </button>
            {showBranchMenu && (
              <div className="absolute right-0 top-full mt-1 w-56 bg-white border-2 border-[#1C1C1C] rounded-xl shadow-[4px_4px_0px_#1C1C1C] z-50">
                {BRANCHES.map((b) => {
                  const dist = b.status === "open" ? `${distance.toFixed(1)}km` : "—";
                  return (
                    <button
                      key={b.id}
                      onClick={() => {
                        if (b.status === "closed") {
                          toast.error(`${b.name} hiện đang đóng cửa`);
                          return;
                        }
                        selectBranch(b.id);
                        setShowBranchMenu(false);
                        toast.success(`Đã chọn ${b.name}`, {
                          description: `Khoảng cách: ${dist}`,
                        });
                      }}
                      className="w-full text-left px-3 py-2 flex items-center gap-2 hover:bg-[#FFD23F]/30 first:rounded-t-lg last:rounded-b-lg disabled:opacity-50"
                    >
                      <div
                        className={`w-2 h-2 rounded-full ${
                          b.status === "open" ? "bg-green-500" : "bg-red-500"
                        }`}
                      />
                      <div className="flex-1">
                        <span className="text-sm">{b.name}</span>
                        {b.status === "open" && (
                          <span className="text-[10px] text-gray-400 ml-1">
                            ({dist})
                          </span>
                        )}
                      </div>
                      {b.status === "closed" && (
                        <span className="text-xs text-gray-400">Đóng cửa</span>
                      )}
                    </button>
                  );
                })}
              </div>
            )}
          </div>

          {/* Notification */}
          <div className="relative">
            <button
              onClick={() => setShowNotif(!showNotif)}
              className="relative w-8 h-8 border-2 border-[#1C1C1C] rounded-lg bg-white flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C]"
            >
              <Bell size={16} />
              {unreadCount > 0 && (
                <span className="absolute -top-1 -right-1 w-4 h-4 bg-[#FF6B35] border border-white rounded-full flex items-center justify-center text-[9px] text-white font-bold">
                  {unreadCount}
                </span>
              )}
            </button>
            {showNotif && (
              <div className="absolute right-0 top-full mt-1 w-64 bg-white border-2 border-[#1C1C1C] rounded-xl shadow-[4px_4px_0px_#1C1C1C] z-50 p-2">
                <p className="text-xs font-black text-[#1C1C1C] px-2 py-1 uppercase">
                  Thông báo
                </p>
                {notifs.length === 0 ? (
                  <p className="text-xs text-gray-400 text-center py-3">
                    Không có thông báo
                  </p>
                ) : (
                  notifs.map((n: (typeof NOTIFICATIONS)[0]) => (
                    <button
                      key={n.id}
                      onClick={() => markRead(n.id)}
                      className={`w-full text-left text-xs px-2 py-2 rounded-lg transition-all ${
                        n.read
                          ? "text-gray-400"
                          : "text-[#1C1C1C] font-medium bg-[#FFD23F]/20"
                      }`}
                    >
                      {n.text}
                    </button>
                  ))
                )}
              </div>
            )}
          </div>
        </div>

        {/* Search */}
        <div className="relative">
          <Search
            size={16}
            className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer hover:text-[#FF6B35] transition-colors"
            onClick={handleSearchClick}
          />
          <input
            type="text"
            value={searchQuery}
            onChange={(e: React.ChangeEvent<HTMLInputElement>) => setSearchQuery(e.target.value)}
            onKeyDown={handleSearch}
            placeholder="Tìm món ăn, đồ uống..."
            className="w-full pl-9 pr-4 py-2.5 border-2 border-[#1C1C1C] rounded-xl bg-white shadow-[2px_2px_0px_#1C1C1C] text-sm outline-none focus:shadow-[3px_3px_0px_#FF6B35] focus:border-[#FF6B35] transition-all"
          />
        </div>
      </header>

      {/* Main content */}
      <main className="flex-1 overflow-y-auto pb-24">
        <Outlet />
      </main>

      {/* Admin quick access */}
      <div className="fixed bottom-20 right-4 z-40">
        <button
          onClick={() => navigate("/admin/kds")}
          className="bg-[#1C1C1C] text-white text-xs px-3 py-2 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#FF6B35] font-bold hover:scale-105 transition-transform"
        >
          🍳 Admin
        </button>
      </div>

      {/* Bottom nav */}
      <nav className="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] bg-white border-t-2 border-[#1C1C1C] z-40">
        <div className="flex items-center justify-around px-2 py-2">
          {navItems.map((item) => {
            const Icon = item.icon;
            const active = isActive(item.path);
            return (
              <button
                key={item.path}
                onClick={() => navigate(item.path)}
                className={`flex flex-col items-center gap-0.5 px-4 py-1.5 rounded-xl transition-all ${
                  active
                    ? "bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]"
                    : "text-gray-500 hover:text-[#FF6B35]"
                }`}
              >
                <div className="relative">
                  <Icon size={20} />
                  {item.path === "/cart" && totalItems > 0 && (
                    <span className="absolute -top-1.5 -right-1.5 w-4 h-4 bg-[#FFD23F] border border-[#1C1C1C] rounded-full text-[9px] text-[#1C1C1C] font-black flex items-center justify-center">
                      {totalItems}
                    </span>
                  )}
                </div>
                <span className="text-[10px] font-bold">{item.label}</span>
              </button>
            );
          })}
        </div>
      </nav>
    </div>
  );
}

