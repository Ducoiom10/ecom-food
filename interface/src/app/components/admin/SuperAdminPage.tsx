import { useState } from "react";
import { BarChart2, Shield, Bell, Tag, FileText, TrendingUp, Users, DollarSign, Zap, CheckSquare, Square } from "lucide-react";
import { LineChart, Line, AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from "recharts";
import { HISTORICAL_SALES, BRANCHES, formatPrice } from "../../data/mockData";

const ROLES = ["Super Admin", "Branch Manager", "Coordinator", "Kitchen Staff", "Support"];
const PERMISSIONS = [
  { key: "view_revenue", label: "Xem doanh thu" },
  { key: "manage_menu", label: "Quản lý thực đơn" },
  { key: "manage_vouchers", label: "Quản lý voucher" },
  { key: "view_orders", label: "Xem đơn hàng" },
  { key: "update_orders", label: "Cập nhật đơn hàng" },
  { key: "manage_staff", label: "Quản lý nhân viên" },
  { key: "refund_orders", label: "Hoàn tiền đơn hàng" },
  { key: "view_audit_log", label: "Xem audit log" },
];

const ROLE_PERMS: Record<string, string[]> = {
  "Super Admin": ["view_revenue", "manage_menu", "manage_vouchers", "view_orders", "update_orders", "manage_staff", "refund_orders", "view_audit_log"],
  "Branch Manager": ["view_revenue", "manage_menu", "view_orders", "update_orders", "refund_orders"],
  "Coordinator": ["view_orders", "update_orders"],
  "Kitchen Staff": ["view_orders", "update_orders"],
  "Support": ["view_orders", "refund_orders"],
};

const AUDIT_LOGS = [
  { id: 1, time: "13:45:22", user: "admin@baanh.vn", action: "UPDATE", target: "orders/ORD-201", detail: "Status: pending → completed", ip: "192.168.1.10" },
  { id: 2, time: "13:40:15", user: "manager@baanh.vn", action: "UPDATE", target: "vouchers/SALE50", detail: "Discount: 30% → 50%", ip: "192.168.1.25" },
  { id: 3, time: "13:35:08", user: "admin@baanh.vn", action: "DELETE", target: "menu/item-99", detail: "Xoá sản phẩm hết hạn", ip: "192.168.1.10" },
  { id: 4, time: "13:30:01", user: "support@baanh.vn", action: "UPDATE", target: "orders/ORD-185", detail: "Refund approved: 80,000đ", ip: "192.168.1.55" },
  { id: 5, time: "13:20:45", user: "admin@baanh.vn", action: "CREATE", target: "vouchers/LUNCH15K", detail: "Tạo voucher mới giảm 15,000đ", ip: "192.168.1.10" },
  { id: 6, time: "12:55:33", user: "manager@baanh.vn", action: "UPDATE", target: "inventory/beef", detail: "Stock: 15kg → 8kg (tiêu hao)", ip: "192.168.1.25" },
];

const PIE_DATA = [
  { name: "Mì & Phở", value: 35, color: "#FF6B35" },
  { name: "Đồ uống", value: 28, color: "#FFD23F" },
  { name: "Cơm", value: 20, color: "#8B5CF6" },
  { name: "Ăn vặt", value: 17, color: "#22C55E" },
];

const REVENUE_DATA = [
  { day: "T2", revenue: 12500000 },
  { day: "T3", revenue: 14200000 },
  { day: "T4", revenue: 11800000 },
  { day: "T5", revenue: 16500000 },
  { day: "T6", revenue: 18900000 },
  { day: "T7", revenue: 22000000 },
  { day: "CN", revenue: 19500000 },
];

export function SuperAdminPage() {
  const [activeTab, setActiveTab] = useState<"analytics" | "campaigns" | "roles" | "audit">("analytics");
  const [selectedRole, setSelectedRole] = useState("Branch Manager");
  const [rolePerms, setRolePerms] = useState({ ...ROLE_PERMS });
  const [vouchers] = useState([
    { code: "LUNCH15K", discount: "15.000đ", type: "flat", used: 234, max: 1000, status: "active", expires: "30/04/2026" },
    { code: "BANANH20", discount: "20%", type: "percent", used: 89, max: 500, status: "active", expires: "31/05/2026" },
    { code: "FREESHIP", discount: "Free ship", type: "shipping", used: 1000, max: 1000, status: "expired", expires: "20/04/2026" },
  ]);

  const togglePerm = (role: string, perm: string) => {
    if (role === "Super Admin") return; // Can't edit super admin
    setRolePerms((prev) => {
      const perms = prev[role] || [];
      const next = perms.includes(perm) ? perms.filter((p) => p !== perm) : [...perms, perm];
      return { ...prev, [role]: next };
    });
  };

  const totalRevenue = REVENUE_DATA.reduce((sum, d) => sum + d.revenue, 0);

  return (
    <div className="h-full flex flex-col bg-[#0F0F0F]">
      {/* Tabs */}
      <div className="flex border-b-2 border-[#333] bg-[#1A1A1A] flex-shrink-0">
        {([
          { id: "analytics", label: "Analytics", icon: BarChart2 },
          { id: "campaigns", label: "Campaigns", icon: Tag },
          { id: "roles", label: "RBAC Matrix", icon: Shield },
          { id: "audit", label: "Audit Trail", icon: FileText },
        ] as const).map(({ id, label, icon: Icon }) => (
          <button
            key={id}
            onClick={() => setActiveTab(id)}
            className={`flex items-center gap-2 px-5 py-3 text-sm font-black border-r border-[#333] transition-all ${activeTab === id ? "bg-[#FF6B35] text-white" : "text-gray-400 hover:text-white"}`}
          >
            <Icon size={14} />
            {label}
          </button>
        ))}
      </div>

      <div className="flex-1 overflow-y-auto p-6">
        {/* ANALYTICS */}
        {activeTab === "analytics" && (
          <div className="space-y-6">
            {/* KPI row */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
              {[
                { label: "Tổng doanh thu tuần", value: formatPrice(totalRevenue), icon: DollarSign, color: "text-green-400", bg: "bg-green-900/20 border-green-700/30" },
                { label: "Đơn hàng hôm nay", value: "1,247", icon: Zap, color: "text-orange-400", bg: "bg-orange-900/20 border-orange-700/30" },
                { label: "Người dùng active", value: "3,841", icon: Users, color: "text-blue-400", bg: "bg-blue-900/20 border-blue-700/30" },
                { label: "Tăng trưởng tuần", value: "+23%", icon: TrendingUp, color: "text-purple-400", bg: "bg-purple-900/20 border-purple-700/30" },
              ].map((kpi) => {
                const Icon = kpi.icon;
                return (
                  <div key={kpi.label} className={`bg-[#1A1A1A] border-2 ${kpi.bg} rounded-2xl p-4`}>
                    <Icon size={20} className={`${kpi.color} mb-2`} />
                    <div className="text-white font-black text-2xl">{kpi.value}</div>
                    <div className="text-gray-500 text-xs mt-0.5">{kpi.label}</div>
                  </div>
                );
              })}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
              {/* Revenue chart */}
              <div className="lg:col-span-2 bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
                <h3 className="text-white font-black mb-4">Doanh thu 7 ngày qua</h3>
                <ResponsiveContainer width="100%" height={220}>
                  <AreaChart data={REVENUE_DATA}>
                    <defs>
                      <linearGradient id="revenueGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="5%" stopColor="#FF6B35" stopOpacity={0.4} />
                        <stop offset="95%" stopColor="#FF6B35" stopOpacity={0} />
                      </linearGradient>
                    </defs>
                    <CartesianGrid strokeDasharray="3 3" stroke="#222" />
                    <XAxis dataKey="day" tick={{ fontSize: 11, fill: "#666" }} />
                    <YAxis tick={{ fontSize: 10, fill: "#666" }} tickFormatter={(v) => `${(v / 1000000).toFixed(0)}M`} />
                    <Tooltip
                      contentStyle={{ backgroundColor: "#1A1A1A", border: "1px solid #333", borderRadius: "12px" }}
                      labelStyle={{ color: "#fff", fontWeight: "bold" }}
                      formatter={(v: any) => [formatPrice(v), "Doanh thu"]}
                    />
                    <Area type="monotone" dataKey="revenue" stroke="#FF6B35" strokeWidth={2} fill="url(#revenueGrad)" />
                  </AreaChart>
                </ResponsiveContainer>
              </div>

              {/* Pie chart */}
              <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
                <h3 className="text-white font-black mb-4">Doanh thu theo danh mục</h3>
                <ResponsiveContainer width="100%" height={160}>
                  <PieChart>
                    <Pie data={PIE_DATA} cx="50%" cy="50%" innerRadius={40} outerRadius={70} dataKey="value">
                      {PIE_DATA.map((entry, index) => (
                        <Cell key={index} fill={entry.color} />
                      ))}
                    </Pie>
                    <Tooltip formatter={(v: any) => [`${v}%`, "Tỷ trọng"]} contentStyle={{ backgroundColor: "#1A1A1A", border: "1px solid #333", borderRadius: "8px" }} />
                  </PieChart>
                </ResponsiveContainer>
                <div className="space-y-1.5 mt-2">
                  {PIE_DATA.map((d) => (
                    <div key={d.name} className="flex items-center justify-between text-xs">
                      <div className="flex items-center gap-2">
                        <div className="w-3 h-3 rounded-full" style={{ backgroundColor: d.color }} />
                        <span className="text-gray-300">{d.name}</span>
                      </div>
                      <span className="text-white font-bold">{d.value}%</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Branch comparison */}
            <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
              <h3 className="text-white font-black mb-4">So sánh chi nhánh</h3>
              <div className="space-y-3">
                {BRANCHES.filter((b) => b.status === "open").map((branch) => (
                  <div key={branch.id} className="flex items-center gap-4">
                    <span className="text-gray-300 text-sm w-40">{branch.name.replace("Chi nhánh ", "")}</span>
                    <div className="flex-1 h-4 bg-[#333] rounded-full overflow-hidden">
                      <div
                        className="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full"
                        style={{ width: `${(branch.revenue / 16000000) * 100}%` }}
                      />
                    </div>
                    <span className="text-white font-black text-sm w-28 text-right">{formatPrice(branch.revenue)}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {/* CAMPAIGNS */}
        {activeTab === "campaigns" && (
          <div className="space-y-6">
            <div className="flex items-center justify-between">
              <h2 className="text-white font-black text-xl">Quản lý Campaigns</h2>
              <button className="bg-[#FF6B35] text-white text-sm font-bold px-4 py-2 rounded-xl border-2 border-[#FF6B35] flex items-center gap-2">
                <Tag size={14} /> Tạo voucher mới
              </button>
            </div>

            {/* Vouchers */}
            <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
              <table className="w-full">
                <thead className="bg-[#222] border-b border-[#333]">
                  <tr>
                    {["Mã", "Giảm giá", "Loại", "Đã dùng", "Hạn sử dụng", "Trạng thái"].map((h) => (
                      <th key={h} className="text-left text-gray-400 text-xs font-black px-4 py-3 uppercase tracking-wide">{h}</th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {vouchers.map((v) => (
                    <tr key={v.code} className="border-b border-[#222] hover:bg-[#1D1D1D]">
                      <td className="px-4 py-3 text-white font-black text-sm">{v.code}</td>
                      <td className="px-4 py-3 text-[#FFD23F] font-black">{v.discount}</td>
                      <td className="px-4 py-3 text-gray-400 text-sm">{v.type === "flat" ? "Giảm cố định" : v.type === "percent" ? "Giảm %" : "Free ship"}</td>
                      <td className="px-4 py-3">
                        <div className="text-white text-sm font-bold">{v.used}/{v.max}</div>
                        <div className="h-1.5 bg-[#333] rounded-full mt-1 w-20">
                          <div className="h-full bg-[#FF6B35] rounded-full" style={{ width: `${(v.used / v.max) * 100}%` }} />
                        </div>
                      </td>
                      <td className="px-4 py-3 text-gray-400 text-sm">{v.expires}</td>
                      <td className="px-4 py-3">
                        <span className={`text-xs font-black px-2 py-1 rounded-full ${v.status === "active" ? "bg-green-500/20 text-green-400 border border-green-500/30" : "bg-gray-700 text-gray-500"}`}>
                          {v.status === "active" ? "Hoạt động" : "Hết hạn"}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Push notification */}
            <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-5">
              <div className="flex items-center gap-2 mb-4">
                <Bell size={16} className="text-[#FFD23F]" />
                <h3 className="text-white font-black">Gửi Push Notification</h3>
              </div>
              <div className="grid grid-cols-2 gap-3 mb-3">
                <div>
                  <label className="text-gray-400 text-xs mb-1 block">Tiêu đề</label>
                  <input defaultValue="🔥 Flash Sale giờ trưa!" className="w-full bg-[#222] border border-[#444] text-white rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35]" />
                </div>
                <div>
                  <label className="text-gray-400 text-xs mb-1 block">Target segment</label>
                  <select className="w-full bg-[#222] border border-[#444] text-white rounded-xl px-3 py-2 text-sm outline-none">
                    <option>Tất cả người dùng</option>
                    <option>Giỏ hàng bị bỏ quên (Abandoned Cart)</option>
                    <option>Chưa đặt trong 7 ngày</option>
                    <option>Khách VIP (Snack Points &gt; 500)</option>
                  </select>
                </div>
              </div>
              <textarea defaultValue="Đặt ngay Mì trộn + Trà sữa combo chỉ 65k! Áp dụng trong 2 tiếng nữa 🍜🧋" className="w-full bg-[#222] border border-[#444] text-white rounded-xl px-3 py-2 text-sm outline-none focus:border-[#FF6B35] h-20 resize-none mb-3" />
              <button className="bg-[#FF6B35] text-white font-black px-5 py-2.5 rounded-xl border-2 border-[#FF6B35] flex items-center gap-2">
                <Bell size={14} /> Gửi ngay (1,847 users)
              </button>
            </div>
          </div>
        )}

        {/* ROLES */}
        {activeTab === "roles" && (
          <div className="space-y-4">
            <h2 className="text-white font-black text-xl flex items-center gap-2">
              <Shield size={20} className="text-blue-400" />
              RBAC Permission Matrix
            </h2>
            <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full min-w-[700px]">
                  <thead>
                    <tr className="bg-[#222] border-b border-[#333]">
                      <th className="text-left text-gray-400 text-xs px-4 py-3 font-black uppercase tracking-wide">Quyền hạn</th>
                      {ROLES.map((role) => (
                        <th key={role} className="text-center text-gray-300 text-xs px-3 py-3 font-black uppercase">{role}</th>
                      ))}
                    </tr>
                  </thead>
                  <tbody>
                    {PERMISSIONS.map((perm) => (
                      <tr key={perm.key} className="border-b border-[#1D1D1D] hover:bg-[#1D1D1D]">
                        <td className="px-4 py-3 text-gray-300 text-sm">{perm.label}</td>
                        {ROLES.map((role) => {
                          const has = (rolePerms[role] || []).includes(perm.key);
                          const isSuperAdmin = role === "Super Admin";
                          return (
                            <td key={role} className="text-center px-3 py-3">
                              <button
                                onClick={() => togglePerm(role, perm.key)}
                                disabled={isSuperAdmin}
                                className={`mx-auto flex items-center justify-center transition-all ${isSuperAdmin ? "cursor-not-allowed" : "cursor-pointer hover:scale-110"}`}
                              >
                                {has ? (
                                  <CheckSquare size={18} className={isSuperAdmin ? "text-green-300" : "text-green-500"} />
                                ) : (
                                  <Square size={18} className="text-gray-600" />
                                )}
                              </button>
                            </td>
                          );
                        })}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
            <p className="text-gray-600 text-xs">* Super Admin có toàn quyền, không thể chỉnh sửa. Thay đổi sẽ được áp dụng ngay lập tức và ghi vào Audit Log.</p>
          </div>
        )}

        {/* AUDIT */}
        {activeTab === "audit" && (
          <div className="space-y-4">
            <h2 className="text-white font-black text-xl flex items-center gap-2">
              <FileText size={20} className="text-gray-400" />
              Audit Trail
            </h2>
            <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl overflow-hidden">
              <div className="px-4 py-3 border-b border-[#333] flex items-center gap-3">
                <div className="flex-1 bg-[#222] border border-[#444] rounded-xl px-3 py-1.5 flex items-center gap-2">
                  <input placeholder="Tìm kiếm theo user, action, IP..." className="bg-transparent text-white text-sm outline-none w-full" />
                </div>
                <select className="bg-[#222] border border-[#444] text-gray-400 text-xs rounded-xl px-3 py-1.5 outline-none">
                  <option>Tất cả action</option>
                  <option>CREATE</option>
                  <option>UPDATE</option>
                  <option>DELETE</option>
                </select>
              </div>
              <div className="overflow-x-auto">
                <table className="w-full min-w-[700px]">
                  <thead>
                    <tr className="bg-[#222] border-b border-[#333]">
                      {["Thời gian", "Người dùng", "Hành động", "Đối tượng", "Chi tiết", "IP"].map((h) => (
                        <th key={h} className="text-left text-gray-500 text-[10px] px-4 py-2.5 font-black uppercase tracking-wide">{h}</th>
                      ))}
                    </tr>
                  </thead>
                  <tbody>
                    {AUDIT_LOGS.map((log) => (
                      <tr key={log.id} className="border-b border-[#1D1D1D] hover:bg-[#1D1D1D] font-mono text-xs">
                        <td className="px-4 py-3 text-gray-500">{log.time}</td>
                        <td className="px-4 py-3 text-blue-400">{log.user}</td>
                        <td className="px-4 py-3">
                          <span className={`font-black px-2 py-0.5 rounded text-[10px] ${log.action === "CREATE" ? "bg-green-900/50 text-green-400" : log.action === "UPDATE" ? "bg-yellow-900/50 text-yellow-400" : "bg-red-900/50 text-red-400"}`}>
                            {log.action}
                          </span>
                        </td>
                        <td className="px-4 py-3 text-gray-300">{log.target}</td>
                        <td className="px-4 py-3 text-gray-400">{log.detail}</td>
                        <td className="px-4 py-3 text-gray-600">{log.ip}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
