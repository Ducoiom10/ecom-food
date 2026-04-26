import { useState, useEffect } from "react";
import { MapPin, Package, Clock, Truck, CheckCircle, AlertCircle, RefreshCw, Navigation, Phone, Users } from "lucide-react";
import { DISPATCH_ORDERS } from "../../data/mockData";

const STATUS_CONFIG = {
  ready: { label: "Chờ giao", color: "bg-yellow-500 text-black", dot: "bg-yellow-500" },
  picking: { label: "Đang lấy", color: "bg-orange-500 text-white", dot: "bg-orange-500" },
  delivering: { label: "Đang giao", color: "bg-blue-500 text-white", dot: "bg-blue-500" },
  delivered: { label: "Đã giao", color: "bg-green-500 text-white", dot: "bg-green-500" },
};

const SHIPPERS = [
  { id: "s1", name: "Trần Văn B", avatar: "B", status: "busy", orders: 1, distance: "1.2km", phone: "0901234567" },
  { id: "s2", name: "Phạm Văn C", avatar: "C", status: "busy", orders: 1, distance: "0.8km", phone: "0912345678" },
  { id: "s3", name: "Nguyễn Văn D", avatar: "D", status: "busy", orders: 1, distance: "2.1km", phone: "0923456789" },
  { id: "s4", name: "Lê Văn E", avatar: "E", status: "busy", orders: 1, distance: "1.5km", phone: "0934567890" },
  { id: "s5", name: "Hoàng Văn F", avatar: "F", status: "free", orders: 0, distance: "0.5km", phone: "0945678901" },
];

export function DispatchPage() {
  const [orders, setOrders] = useState(DISPATCH_ORDERS);
  const [selectedOrder, setSelectedOrder] = useState<string | null>(null);
  const [selectedShipper, setSelectedShipper] = useState<string | null>(null);
  const [assignModal, setAssignModal] = useState<string | null>(null);
  const [tick, setTick] = useState(0);

  // Simulate live updates
  useEffect(() => {
    const interval = setInterval(() => setTick((t) => t + 1), 10000);
    return () => clearInterval(interval);
  }, []);

  const assignShipper = (orderId: string, shipperId: string) => {
    const shipper = SHIPPERS.find((s) => s.id === shipperId);
    setOrders((prev) =>
      prev.map((o) =>
        o.id === orderId ? { ...o, status: "picking", shipper: shipper?.name || null } : o
      )
    );
    setAssignModal(null);
  };

  const updateStatus = (orderId: string) => {
    setOrders((prev) =>
      prev.map((o) => {
        if (o.id !== orderId) return o;
        const next: Record<string, any> = { ready: "picking", picking: "delivering", delivering: "delivered" };
        return { ...o, status: next[o.status] || o.status };
      })
    );
  };

  // Group orders that share same route/building
  const batchableOrders = orders.filter((o) => o.status === "ready" || o.status === "picking");

  return (
    <div className="h-full flex gap-0 overflow-hidden bg-[#0F0F0F]">
      {/* Left: Map simulation */}
      <div className="flex-1 relative overflow-hidden bg-[#111]">
        {/* Fake map background */}
        <div className="absolute inset-0 opacity-20">
          <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#333" strokeWidth="0.5" />
              </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
            {/* Fake roads */}
            <line x1="0" y1="200" x2="100%" y2="200" stroke="#555" strokeWidth="3" />
            <line x1="0" y1="400" x2="100%" y2="400" stroke="#555" strokeWidth="2" />
            <line x1="200" y1="0" x2="200" y2="100%" stroke="#555" strokeWidth="3" />
            <line x1="450" y1="0" x2="450" y2="100%" stroke="#555" strokeWidth="2" />
            <line x1="0" y1="300" x2="100%" y2="300" stroke="#444" strokeWidth="1" />
          </svg>
        </div>

        {/* Map header */}
        <div className="absolute top-4 left-4 right-4 flex items-center justify-between z-10">
          <div className="bg-[#1A1A1A]/90 backdrop-blur border border-[#333] rounded-xl px-4 py-2 flex items-center gap-2">
            <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse" />
            <span className="text-white text-sm font-black">Live Dispatch Map</span>
          </div>
          <div className="bg-[#1A1A1A]/90 backdrop-blur border border-[#333] rounded-xl px-3 py-2 text-xs text-gray-400 flex items-center gap-1.5">
            <RefreshCw size={12} className="animate-spin" />
            Cập nhật mỗi 10s
          </div>
        </div>

        {/* Shipper markers */}
        {[
          { x: "20%", y: "35%", name: "B", status: "busy", order: "ORD-021" },
          { x: "65%", y: "55%", name: "C", status: "busy", order: "ORD-022" },
          { x: "40%", y: "70%", name: "D", status: "busy", order: "ORD-024" },
          { x: "75%", y: "25%", name: "E", status: "busy", order: "ORD-025" },
          { x: "50%", y: "45%", name: "F", status: "free", order: null },
        ].map((marker) => (
          <div
            key={marker.name}
            className="absolute flex flex-col items-center cursor-pointer transform -translate-x-1/2 -translate-y-1/2"
            style={{ left: marker.x, top: marker.y }}
          >
            <div className={`w-10 h-10 rounded-full border-2 border-white flex items-center justify-center font-black text-white shadow-xl ${marker.status === "free" ? "bg-green-500" : "bg-[#FF6B35]"}`}>
              {marker.name}
            </div>
            {marker.order && (
              <div className="mt-1 bg-[#1A1A1A] border border-[#444] text-white text-[9px] font-bold px-2 py-0.5 rounded-full">
                {marker.order}
              </div>
            )}
            <div className={`mt-0.5 w-2 h-2 rounded-full animate-ping ${marker.status === "free" ? "bg-green-500" : "bg-orange-500"}`} />
          </div>
        ))}

        {/* Restaurant marker */}
        <div className="absolute" style={{ left: "55%", top: "50%" }}>
          <div className="w-8 h-8 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-xl flex items-center justify-center shadow-xl text-lg transform -translate-x-1/2 -translate-y-1/2">
            🍜
          </div>
        </div>

        {/* Batch highlight */}
        {batchableOrders.length >= 2 && (
          <div className="absolute bottom-4 left-4 bg-[#FFD23F]/90 border-2 border-[#1C1C1C] rounded-xl px-4 py-3 max-w-xs">
            <div className="flex items-center gap-2 mb-1">
              <Users size={14} className="text-[#1C1C1C]" />
              <span className="font-black text-[#1C1C1C] text-sm">Gom đơn thông minh</span>
            </div>
            <p className="text-xs text-[#1C1C1C]/80">{batchableOrders.length} đơn trong bán kính Q1 · Có thể gom 1 chuyến ship!</p>
          </div>
        )}
      </div>

      {/* Right: Orders list */}
      <div className="w-96 flex-shrink-0 bg-[#1A1A1A] border-l-2 border-[#333] flex flex-col">
        {/* Stats */}
        <div className="grid grid-cols-3 border-b-2 border-[#333]">
          {[
            { label: "Đang active", value: orders.filter((o) => o.status !== "delivered").length, color: "text-orange-400" },
            { label: "Shipper", value: SHIPPERS.filter((s) => s.status === "free").length + " rảnh", color: "text-green-400" },
            { label: "Hôm nay", value: "47 đơn", color: "text-blue-400" },
          ].map((s) => (
            <div key={s.label} className="px-4 py-3 text-center border-r last:border-r-0 border-[#333]">
              <div className={`font-black text-lg ${s.color}`}>{s.value}</div>
              <div className="text-gray-500 text-[10px]">{s.label}</div>
            </div>
          ))}
        </div>

        {/* Order list */}
        <div className="flex-1 overflow-y-auto">
          {orders.map((order) => {
            const cfg = STATUS_CONFIG[order.status as keyof typeof STATUS_CONFIG] || STATUS_CONFIG.ready;
            return (
              <div
                key={order.id}
                onClick={() => setSelectedOrder(selectedOrder === order.id ? null : order.id)}
                className={`border-b border-[#222] p-4 cursor-pointer transition-all ${selectedOrder === order.id ? "bg-[#252525]" : "hover:bg-[#1D1D1D]"}`}
              >
                <div className="flex items-start gap-3">
                  <div className="w-8 h-8 bg-[#FF6B35] rounded-xl flex items-center justify-center flex-shrink-0">
                    <Package size={14} className="text-white" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between mb-1">
                      <span className="text-white font-black text-sm">{order.id}</span>
                      <span className={`text-[10px] font-black px-2 py-0.5 rounded-full ${cfg.color}`}>{cfg.label}</span>
                    </div>
                    <div className="text-gray-400 text-xs truncate">{order.customer}</div>
                    <div className="flex items-center gap-1 text-gray-500 text-[10px] mt-0.5">
                      <MapPin size={10} />
                      <span className="truncate">{order.address}</span>
                    </div>
                    <div className="flex items-center justify-between mt-2">
                      <div className="flex items-center gap-1 text-gray-400 text-xs">
                        <Truck size={10} />
                        <span>{order.shipper || "Chưa phân công"}</span>
                      </div>
                      <div className="flex items-center gap-1 text-gray-400 text-xs">
                        <Clock size={10} />
                        <span>ETA: {order.eta}</span>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Expanded actions */}
                {selectedOrder === order.id && (
                  <div className="mt-3 pt-3 border-t border-[#333] space-y-2">
                    <div className="flex gap-2">
                      {!order.shipper && (
                        <button
                          onClick={(e) => { e.stopPropagation(); setAssignModal(order.id); }}
                          className="flex-1 text-xs font-bold bg-[#FFD23F] text-[#1C1C1C] py-2 rounded-xl border border-[#444] flex items-center justify-center gap-1"
                        >
                          <Truck size={12} /> Phân shipper
                        </button>
                      )}
                      <button
                        onClick={(e) => { e.stopPropagation(); updateStatus(order.id); }}
                        className="flex-1 text-xs font-bold bg-[#FF6B35] text-white py-2 rounded-xl border border-[#FF6B35] flex items-center justify-center gap-1"
                      >
                        <CheckCircle size={12} /> Cập nhật
                      </button>
                    </div>
                    {order.shipper && (
                      <a href={`tel:${order.shipperPhone}`} onClick={(e) => e.stopPropagation()} className="flex items-center gap-2 text-xs text-blue-400 hover:text-blue-300">
                        <Phone size={12} /> Gọi {order.shipper}: {order.shipperPhone}
                      </a>
                    )}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </div>

      {/* Assign shipper modal */}
      {assignModal && (
        <div className="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
          <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-5 w-80 shadow-2xl">
            <h3 className="text-white font-black text-lg mb-4">Phân công Shipper</h3>
            <div className="space-y-2 mb-4">
              {SHIPPERS.map((s) => (
                <button
                  key={s.id}
                  onClick={() => assignShipper(assignModal, s.id)}
                  className={`w-full flex items-center gap-3 p-3 rounded-xl border transition-all ${s.status === "free" ? "border-green-500/50 hover:border-green-500 hover:bg-green-900/20" : "border-[#333] opacity-60"}`}
                >
                  <div className={`w-9 h-9 rounded-full flex items-center justify-center font-black text-white ${s.status === "free" ? "bg-green-500" : "bg-gray-600"}`}>
                    {s.avatar}
                  </div>
                  <div className="text-left flex-1">
                    <div className="text-white text-sm font-bold">{s.name}</div>
                    <div className="text-gray-400 text-xs">{s.status === "free" ? `Rảnh · ${s.distance}` : `Đang giao ${s.orders} đơn`}</div>
                  </div>
                  {s.status === "free" && <span className="text-green-400 text-xs font-bold">Chọn</span>}
                </button>
              ))}
            </div>
            <button onClick={() => setAssignModal(null)} className="w-full text-gray-400 text-sm hover:text-white">Huỷ</button>
          </div>
        </div>
      )}
    </div>
  );
}
