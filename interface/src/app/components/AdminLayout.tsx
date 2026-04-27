import { Outlet, useNavigate, useLocation } from "react-router";
import { UtensilsCrossed, Truck, BarChart3, Settings, ChefHat, ArrowLeft, Brain } from "lucide-react";

export function AdminLayout() {
  const navigate = useNavigate();
  const location = useLocation();

  const navItems = [
    { path: "/admin/kds", icon: ChefHat, label: "Bếp KDS", color: "bg-orange-500" },
    { path: "/admin/smart-prep", icon: Brain, label: "Smart Prep", color: "bg-purple-500" },
    { path: "/admin/dispatch", icon: Truck, label: "Điều phối", color: "bg-blue-500" },
    { path: "/admin/branch", icon: UtensilsCrossed, label: "Chi nhánh", color: "bg-green-500" },
    { path: "/admin/super", icon: BarChart3, label: "Super Admin", color: "bg-red-500" },
  ];

  const isActive = (path: string) => location.pathname === path;
  const currentPage = navItems.find((n) => isActive(n.path));

  return (
    <div className="min-h-screen bg-[#0F0F0F] text-white flex">
      {/* Sidebar */}
      <aside className="w-16 lg:w-56 flex-shrink-0 bg-[#1A1A1A] border-r-2 border-[#333] flex flex-col">
        {/* Logo */}
        <div className="p-4 border-b-2 border-[#333]">
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 bg-[#FF6B35] border-2 border-[#FF6B35] rounded-lg flex items-center justify-center flex-shrink-0">
              <span className="text-sm">🍜</span>
            </div>
            <div className="hidden lg:block">
              <div className="font-black text-white text-sm">Ba Anh Em</div>
              <div className="text-[10px] text-gray-400 uppercase tracking-wider">Admin Portal</div>
            </div>
          </div>
        </div>

        {/* Nav */}
        <nav className="flex-1 p-2 space-y-1">
          {navItems.map((item) => {
            const Icon = item.icon;
            const active = isActive(item.path);
            return (
              <button
                key={item.path}
                onClick={() => navigate(item.path)}
                className={`w-full flex items-center gap-3 px-2 py-3 rounded-xl transition-all text-left ${active ? `${item.color} text-white shadow-lg` : "text-gray-400 hover:bg-[#252525] hover:text-white"}`}
              >
                <Icon size={18} className="flex-shrink-0" />
                <span className="hidden lg:block text-sm font-bold">{item.label}</span>
              </button>
            );
          })}
        </nav>

        {/* Back to app */}
        <div className="p-2 border-t-2 border-[#333]">
          <button
            onClick={() => navigate("/")}
            className="w-full flex items-center gap-3 px-2 py-3 rounded-xl text-gray-400 hover:bg-[#252525] hover:text-white transition-all"
          >
            <ArrowLeft size={18} className="flex-shrink-0" />
            <span className="hidden lg:block text-sm font-bold">App khách hàng</span>
          </button>
        </div>
      </aside>

      {/* Main */}
      <main className="flex-1 flex flex-col overflow-hidden">
        {/* Top bar */}
        <header className="bg-[#1A1A1A] border-b-2 border-[#333] px-6 py-3 flex items-center justify-between flex-shrink-0">
          <div>
            <h1 className="font-black text-white text-lg">{currentPage?.label ?? "Admin"}</h1>
            <p className="text-gray-400 text-xs">Hệ thống quản trị Ba Anh Em · {new Date().toLocaleDateString("vi-VN", { weekday: "long", day: "numeric", month: "long" })}</p>
          </div>
          <div className="flex items-center gap-3">
            <div className="flex items-center gap-2 bg-green-500/10 border border-green-500/30 px-3 py-1.5 rounded-lg">
              <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse" />
              <span className="text-green-400 text-xs font-bold">Hệ thống hoạt động</span>
            </div>
            <div className="w-8 h-8 bg-[#FF6B35] rounded-full flex items-center justify-center text-sm font-black">A</div>
          </div>
        </header>

        <div className="flex-1 overflow-auto flex flex-col">
          <Outlet />
        </div>
      </main>
    </div>
  );
}
