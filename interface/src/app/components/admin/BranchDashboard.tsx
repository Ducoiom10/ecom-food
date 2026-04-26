import { useState } from "react";
import { TrendingUp, TrendingDown, AlertTriangle, Package, DollarSign, ShoppingBag, Star, CheckCircle, XCircle, Clock } from "lucide-react";
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from "recharts";
import { BRANCHES, INVENTORY_ITEMS, HISTORICAL_SALES, formatPrice } from "../../data/mockData";

const REFUND_REQUESTS = [
  { id: "ref1", order: "ORD-198", customer: "Nguyễn V. Anh", reason: "Thiếu món (gà rán)", amount: 55000, status: "pending", time: "12:34" },
  { id: "ref2", order: "ORD-201", customer: "Lê Thị Hoa", reason: "Món bị lạnh khi giao", amount: 35000, status: "pending", time: "13:10" },
  { id: "ref3", order: "ORD-185", customer: "Trần V. Bình", reason: "Sai món đặt", amount: 80000, status: "approved", time: "11:20" },
];

const HOURLY_DATA = HISTORICAL_SALES.slice(5, 15);

export function BranchDashboard() {
  const [selectedBranch, setSelectedBranch] = useState(BRANCHES[0]);
  const [refunds, setRefunds] = useState(REFUND_REQUESTS);

  const handleRefund = (id: string, approved: boolean) => {
    setRefunds((prev) =>
      prev.map((r) => r.id === id ? { ...r, status: approved ? "approved" : "rejected" } : r)
    );
  };

  const lowStock = INVENTORY_ITEMS.filter((i) => i.status === "low" || i.status === "critical");

  return (
    <div className="h-full overflow-y-auto bg-[#0F0F0F] p-6">
      {/* Branch selector */}
      <div className="flex gap-2 mb-6 flex-wrap">
        {BRANCHES.map((branch) => (
          <button
            key={branch.id}
            onClick={() => setSelectedBranch(branch)}
            className={`flex items-center gap-2 px-4 py-2 rounded-xl border-2 text-sm font-bold transition-all ${selectedBranch.id === branch.id ? "bg-[#FF6B35] text-white border-[#FF6B35]" : "bg-[#1A1A1A] text-gray-400 border-[#333] hover:border-[#FF6B35]/50"} ${branch.status === "closed" ? "opacity-50" : ""}`}
            disabled={branch.status === "closed"}
          >
            <div className={`w-2 h-2 rounded-full ${branch.status === "open" ? "bg-green-500" : "bg-red-500"}`} />
            {branch.name.replace("Chi nhánh ", "")}
            {branch.status === "closed" && <span className="text-[10px]">(Đóng)</span>}
          </button>
        ))}
      </div>

      {/* KPI cards */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {[
          { label: "Doanh thu hôm nay", value: formatPrice(selectedBranch.revenue), change: "+12%", up: true, icon: DollarSign, color: "text-green-400" },
          { label: "Tổng đơn", value: `${selectedBranch.orders} đơn`, change: "+8%", up: true, icon: ShoppingBag, color: "text-blue-400" },
          { label: "Đánh giá TB", value: `${selectedBranch.rating} ⭐`, change: "+0.1", up: true, icon: Star, color: "text-yellow-400" },
          { label: "TB đơn/giờ", value: `${Math.round(selectedBranch.orders / 14)} đơn`, change: "bình thường", up: true, icon: TrendingUp, color: "text-orange-400" },
        ].map((kpi) => {
          const Icon = kpi.icon;
          return (
            <div key={kpi.label} className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
              <div className="flex items-center justify-between mb-2">
                <Icon size={18} className={kpi.color} />
                <span className={`text-xs font-bold flex items-center gap-0.5 ${kpi.up ? "text-green-400" : "text-red-400"}`}>
                  {kpi.up ? <TrendingUp size={10} /> : <TrendingDown size={10} />}
                  {kpi.change}
                </span>
              </div>
              <div className="text-white font-black text-xl">{kpi.value}</div>
              <div className="text-gray-500 text-xs mt-0.5">{kpi.label}</div>
            </div>
          );
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Revenue chart */}
        <div className="lg:col-span-2 bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
          <h3 className="text-white font-black mb-4">Doanh thu theo giờ - {selectedBranch.name}</h3>
          <ResponsiveContainer width="100%" height={200}>
            <BarChart data={HOURLY_DATA} margin={{ top: 0, right: 0, left: -20, bottom: 0 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#222" />
              <XAxis dataKey="hour" tick={{ fontSize: 10, fill: "#666" }} />
              <YAxis tick={{ fontSize: 10, fill: "#666" }} />
              <Tooltip
                contentStyle={{ backgroundColor: "#1A1A1A", border: "1px solid #333", borderRadius: "12px" }}
                labelStyle={{ color: "#fff", fontWeight: "bold" }}
              />
              <Bar dataKey="miBow" name="Mì trộn" fill="#FF6B35" radius={[4, 4, 0, 0]} />
              <Bar dataKey="pho" name="Phở" fill="#FFD23F" radius={[4, 4, 0, 0]} />
              <Bar dataKey="drinks" name="Đồ uống" fill="#8B5CF6" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>

        {/* Right panel */}
        <div className="space-y-4">
          {/* Inventory alerts */}
          <div className="bg-[#1A1A1A] border-2 border-red-500/30 rounded-2xl p-4">
            <div className="flex items-center gap-2 mb-3">
              <AlertTriangle size={16} className="text-red-400" />
              <span className="text-white font-black text-sm">Cảnh báo tồn kho</span>
              <span className="bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">{lowStock.length}</span>
            </div>
            <div className="space-y-2">
              {lowStock.map((item) => (
                <div key={item.id} className={`flex items-center justify-between text-xs rounded-xl px-3 py-2 ${item.status === "critical" ? "bg-red-900/30 border border-red-700/30" : "bg-yellow-900/20 border border-yellow-700/20"}`}>
                  <span className="text-white font-bold">{item.name}</span>
                  <div className="text-right">
                    <span className={`font-black ${item.status === "critical" ? "text-red-400" : "text-yellow-400"}`}>
                      {item.current}/{item.safety} {item.unit}
                    </span>
                    <div className={`text-[9px] ${item.status === "critical" ? "text-red-400" : "text-yellow-400"}`}>
                      {item.status === "critical" ? "HẾT GẦN!" : "THẤP"}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Refund resolution */}
          <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
            <div className="flex items-center gap-2 mb-3">
              <Package size={16} className="text-[#FF6B35]" />
              <span className="text-white font-black text-sm">Yêu cầu hoàn tiền</span>
              <span className="bg-[#FF6B35] text-white text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">
                {refunds.filter((r) => r.status === "pending").length}
              </span>
            </div>
            <div className="space-y-3">
              {refunds.map((refund) => (
                <div key={refund.id} className={`rounded-xl p-3 border ${refund.status === "pending" ? "bg-orange-900/20 border-orange-700/30" : refund.status === "approved" ? "bg-green-900/20 border-green-700/30" : "bg-red-900/20 border-red-700/30"}`}>
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-white text-xs font-black">{refund.order}</span>
                    <span className={`text-[10px] font-black ${refund.status === "pending" ? "text-orange-400" : refund.status === "approved" ? "text-green-400" : "text-red-400"}`}>
                      {refund.status === "pending" ? "Chờ duyệt" : refund.status === "approved" ? "Đã duyệt" : "Từ chối"}
                    </span>
                  </div>
                  <div className="text-gray-400 text-[10px] mb-1">{refund.customer} · {refund.reason}</div>
                  <div className="flex items-center justify-between">
                    <span className="text-[#FF6B35] font-black text-sm">{formatPrice(refund.amount)}</span>
                    {refund.status === "pending" && (
                      <div className="flex gap-1">
                        <button onClick={() => handleRefund(refund.id, true)} className="text-[10px] font-black bg-green-600 text-white px-2 py-1 rounded-lg flex items-center gap-0.5">
                          <CheckCircle size={10} /> OK
                        </button>
                        <button onClick={() => handleRefund(refund.id, false)} className="text-[10px] font-black bg-red-600 text-white px-2 py-1 rounded-lg flex items-center gap-0.5">
                          <XCircle size={10} /> TK
                        </button>
                      </div>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
