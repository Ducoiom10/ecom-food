import { useState } from "react";
import { Outlet, useNavigate, useLocation } from "react-router";
import { Home, UtensilsCrossed, ShoppingCart, User, Search, MapPin, ChevronDown, Truck, Store, Bell } from "lucide-react";

export function Layout() {
  const navigate = useNavigate();
  const location = useLocation();
  const [orderMode, setOrderMode] = useState<"delivery" | "pickup">("delivery");
  const [cartCount] = useState(2);
  const [branch, setBranch] = useState("Chi nhánh Quận 1");
  const [showBranchMenu, setShowBranchMenu] = useState(false);

  const branches = [
    { name: "Chi nhánh Quận 1", status: "open" },
    { name: "Chi nhánh Quận 3", status: "open" },
    { name: "Chi nhánh Bình Thạnh", status: "open" },
    { name: "Chi nhánh Gò Vấp", status: "closed" },
  ];

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
              <span className="max-w-[100px] truncate font-medium">{branch}</span>
              <ChevronDown size={12} />
            </button>
            {showBranchMenu && (
              <div className="absolute right-0 top-full mt-1 w-52 bg-white border-2 border-[#1C1C1C] rounded-xl shadow-[4px_4px_0px_#1C1C1C] z-50">
                {branches.map((b) => (
                  <button
                    key={b.name}
                    onClick={() => { setBranch(b.name); setShowBranchMenu(false); }}
                    disabled={b.status === "closed"}
                    className="w-full text-left px-3 py-2 flex items-center gap-2 hover:bg-[#FFD23F]/30 first:rounded-t-lg last:rounded-b-lg disabled:opacity-50"
                  >
                    <div className={`w-2 h-2 rounded-full ${b.status === "open" ? "bg-green-500" : "bg-red-500"}`} />
                    <span className="text-sm">{b.name}</span>
                    {b.status === "closed" && <span className="text-xs text-gray-400 ml-auto">Đóng cửa</span>}
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Notification */}
          <button className="relative w-8 h-8 border-2 border-[#1C1C1C] rounded-lg bg-white flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C]">
            <Bell size={16} />
            <span className="absolute -top-1 -right-1 w-4 h-4 bg-[#FF6B35] border border-white rounded-full flex items-center justify-center text-[9px] text-white font-bold">3</span>
          </button>
        </div>

        {/* Order mode toggle */}
        <div className="flex gap-2 mb-3">
          <button
            onClick={() => setOrderMode("delivery")}
            className={`flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold transition-all ${orderMode === "delivery" ? "bg-[#FF6B35] text-white shadow-[3px_3px_0px_#1C1C1C]" : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C]"}`}
          >
            <Truck size={14} />
            Giao hàng
          </button>
          <button
            onClick={() => setOrderMode("pickup")}
            className={`flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl border-2 border-[#1C1C1C] text-sm font-bold transition-all ${orderMode === "pickup" ? "bg-[#1C1C1C] text-white shadow-[3px_3px_0px_#FF6B35]" : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C]"}`}
          >
            <Store size={14} />
            Tự đến lấy
          </button>
        </div>

        {/* Search */}
        <div className="relative">
          <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input
            type="text"
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
                className={`flex flex-col items-center gap-0.5 px-4 py-1.5 rounded-xl transition-all ${active ? "bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]" : "text-gray-500 hover:text-[#FF6B35]"}`}
              >
                <div className="relative">
                  <Icon size={20} />
                  {item.path === "/cart" && cartCount > 0 && (
                    <span className="absolute -top-1.5 -right-1.5 w-4 h-4 bg-[#FFD23F] border border-[#1C1C1C] rounded-full text-[9px] text-[#1C1C1C] font-black flex items-center justify-center">
                      {cartCount}
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
