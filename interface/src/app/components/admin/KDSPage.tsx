import { useState, useEffect, useRef, useCallback } from "react";
import { WifiOff, Wifi, RefreshCw, Check, ChefHat, Clock, AlertTriangle, Zap, Database, Upload, Bell, Eye, EyeOff, Package } from "lucide-react";
import { KDS_ORDERS, INVENTORY_ITEMS } from "../../data/mockData";

type OrderStatus = "todo" | "cooking" | "ready" | "done";

interface KDSOrder {
  id: string;
  table: string;
  items: { name: string; qty: number; note: string; toppings: string[] }[];
  status: OrderStatus;
  time: string;
  elapsed: number;
  priority: "normal" | "high";
}

interface SyncQueueItem {
  id: string;
  orderId: string;
  action: string;
  newStatus: OrderStatus;
  timestamp: number;
  synced: boolean;
}

interface InventoryItem {
  id: string;
  name: string;
  unit: string;
  current: number;
  safety: number;
  max: number;
  status: "ok" | "low" | "critical";
  isAvailable: boolean;
}

export function KDSPage() {
  const [orders, setOrders] = useState<KDSOrder[]>(KDS_ORDERS as KDSOrder[]);
  const [inventory, setInventory] = useState<InventoryItem[]>(
    INVENTORY_ITEMS.map((item) => ({ ...item, isAvailable: item.status !== "critical" }))
  );
  const [isOnline, setIsOnline] = useState(navigator.onLine);
  const [syncQueue, setSyncQueue] = useState<SyncQueueItem[]>([]);
  const [isSyncing, setIsSyncing] = useState(false);
  const [syncedCount, setSyncedCount] = useState(0);
  const [showSyncPanel, setShowSyncPanel] = useState(false);
  const [activeView, setActiveView] = useState<"kanban" | "inventory">("kanban");
  const [elapsedTick, setElapsedTick] = useState(0);
  const [newOrderAlert, setNewOrderAlert] = useState(false);
  const [showOfflineBanner, setShowOfflineBanner] = useState(false);
  const syncTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  // Timer tick for elapsed time
  useEffect(() => {
    const interval = setInterval(() => setElapsedTick((t) => t + 1), 30000);
    return () => clearInterval(interval);
  }, []);

  // Network status listener
  useEffect(() => {
    const handleOnline = () => {
      setIsOnline(true);
      setShowOfflineBanner(false);
      // Auto-sync when back online
      if (syncQueue.filter((q) => !q.synced).length > 0) {
        setTimeout(() => processSyncQueue(), 1000);
      }
    };
    const handleOffline = () => {
      setIsOnline(false);
      setShowOfflineBanner(true);
    };
    window.addEventListener("online", handleOnline);
    window.addEventListener("offline", handleOffline);
    return () => {
      window.removeEventListener("online", handleOnline);
      window.removeEventListener("offline", handleOffline);
    };
  }, [syncQueue]);

  // Simulate new order arriving every 45s
  useEffect(() => {
    const timer = setTimeout(() => {
      const newOrder: KDSOrder = {
        id: `ORD-${Date.now().toString().slice(-3)}`,
        table: Math.random() > 0.5 ? `Bàn ${Math.floor(Math.random() * 12) + 1}` : "Ship - Khách mới",
        items: [{ name: "Mì trộn đặc biệt", qty: 2, note: "Ít hành", toppings: ["Trứng lòng đào"] }],
        status: "todo",
        time: new Date().toLocaleTimeString("vi-VN", { hour: "2-digit", minute: "2-digit" }),
        elapsed: 0,
        priority: Math.random() > 0.7 ? "high" : "normal",
      };
      setOrders((prev) => [newOrder, ...prev]);
      setNewOrderAlert(true);
      setTimeout(() => setNewOrderAlert(false), 4000);
    }, 45000);
    return () => clearTimeout(timer);
  }, []);

  const processSyncQueue = useCallback(() => {
    const pending = syncQueue.filter((q) => !q.synced);
    if (pending.length === 0 || !isOnline) return;

    setIsSyncing(true);
    // Simulate API calls
    let delay = 0;
    pending.forEach((item) => {
      delay += 600;
      setTimeout(() => {
        setSyncQueue((prev) =>
          prev.map((q) => (q.id === item.id ? { ...q, synced: true } : q))
        );
        setSyncedCount((c) => c + 1);
      }, delay);
    });
    setTimeout(() => {
      setIsSyncing(false);
    }, delay + 200);
  }, [syncQueue, isOnline]);

  const moveOrder = (orderId: string, newStatus: OrderStatus) => {
    setOrders((prev) =>
      prev.map((o) => (o.id === orderId ? { ...o, status: newStatus } : o))
    );

    const queueItem: SyncQueueItem = {
      id: `sync-${Date.now()}`,
      orderId,
      action: `Chuyển trạng thái → ${newStatus}`,
      newStatus,
      timestamp: Date.now(),
      synced: isOnline, // If online, mark synced immediately
    };
    setSyncQueue((prev) => [...prev, queueItem]);

    if (!isOnline) {
      // Save to IndexedDB/localStorage simulation
      const pending = JSON.parse(localStorage.getItem("kds_pending_sync") || "[]");
      pending.push(queueItem);
      localStorage.setItem("kds_pending_sync", JSON.stringify(pending));
    }
  };

  const toggleInventory = (itemId: string) => {
    setInventory((prev) =>
      prev.map((item) =>
        item.id === itemId ? { ...item, isAvailable: !item.isAvailable } : item
      )
    );
  };

  const pendingSync = syncQueue.filter((q) => !q.synced);
  const todoOrders = orders.filter((o) => o.status === "todo");
  const cookingOrders = orders.filter((o) => o.status === "cooking");
  const readyOrders = orders.filter((o) => o.status === "ready");

  const getElapsedColor = (elapsed: number) => {
    if (elapsed < 10) return "text-green-400";
    if (elapsed < 20) return "text-yellow-400";
    return "text-red-400 animate-pulse";
  };

  const getCardBorderColor = (order: KDSOrder) => {
    if (order.priority === "high") return "border-[#FF6B35] shadow-[4px_4px_0px_#FF6B35]";
    if (order.elapsed > 20) return "border-red-500 shadow-[4px_4px_0px_#ef4444]";
    return "border-[#333] shadow-[4px_4px_0px_#333]";
  };

  return (
    <div className="flex flex-col h-full bg-[#0F0F0F]">
      {/* Offline Banner */}
      {showOfflineBanner && (
        <div className="bg-red-600 border-b-2 border-red-400 px-6 py-3 flex items-center gap-3 z-50">
          <WifiOff size={20} className="text-white flex-shrink-0" />
          <div className="flex-1">
            <p className="text-white font-black text-sm">Mất kết nối mạng!</p>
            <p className="text-red-200 text-xs">Đang hoạt động ở chế độ Offline · Hành động sẽ được sync khi có mạng trở lại</p>
          </div>
          <div className="bg-red-700 text-white text-xs font-black px-3 py-1.5 rounded-lg border border-red-400 animate-pulse">
            OFFLINE MODE
          </div>
        </div>
      )}

      {/* New Order Alert */}
      {newOrderAlert && (
        <div className="bg-[#FFD23F] border-b-2 border-[#1C1C1C] px-6 py-2 flex items-center gap-3 animate-bounce">
          <Bell size={18} className="text-[#1C1C1C]" />
          <p className="text-[#1C1C1C] font-black text-sm">🔔 Đơn mới vừa vào! Kiểm tra cột "Cần làm"</p>
        </div>
      )}

      {/* KDS Header */}
      <div className="bg-[#1A1A1A] border-b-2 border-[#333] px-6 py-3 flex items-center justify-between flex-shrink-0">
        <div className="flex items-center gap-4">
          <div className="flex items-center gap-2">
            {isOnline ? (
              <><Wifi size={16} className="text-green-500" /><span className="text-green-400 text-xs font-bold">Online</span></>
            ) : (
              <><WifiOff size={16} className="text-red-500" /><span className="text-red-400 text-xs font-bold">Offline</span></>
            )}
          </div>
          {/* Simulate offline toggle for demo */}
          <button
            onClick={() => {
              setIsOnline((prev) => {
                const next = !prev;
                if (next) setShowOfflineBanner(false);
                else setShowOfflineBanner(true);
                return next;
              });
            }}
            className={`text-xs px-3 py-1.5 rounded-lg border font-bold transition-all ${isOnline ? "border-gray-600 text-gray-400 hover:border-red-500 hover:text-red-400" : "border-green-500 text-green-400 hover:bg-green-500/10"}`}
          >
            {isOnline ? "Mô phỏng Offline" : "Kết nối lại"}
          </button>
        </div>

        <div className="flex items-center gap-3">
          {/* Sync queue indicator */}
          {pendingSync.length > 0 && (
            <button
              onClick={() => setShowSyncPanel(!showSyncPanel)}
              className="flex items-center gap-2 bg-orange-500/20 border border-orange-500/50 px-3 py-1.5 rounded-lg"
            >
              <Database size={14} className="text-orange-400" />
              <span className="text-orange-400 text-xs font-bold">{pendingSync.length} chờ sync</span>
              {isSyncing && <RefreshCw size={14} className="text-orange-400 animate-spin" />}
            </button>
          )}
          {syncedCount > 0 && pendingSync.length === 0 && (
            <div className="flex items-center gap-1.5 bg-green-500/20 border border-green-500/30 px-3 py-1.5 rounded-lg">
              <Upload size={14} className="text-green-400" />
              <span className="text-green-400 text-xs font-bold">Đã sync {syncedCount} hành động</span>
            </div>
          )}

          {/* View toggle */}
          <div className="flex border border-[#444] rounded-xl overflow-hidden">
            <button onClick={() => setActiveView("kanban")} className={`px-3 py-1.5 text-xs font-bold transition-all ${activeView === "kanban" ? "bg-[#FF6B35] text-white" : "text-gray-400 hover:text-white"}`}>
              <ChefHat size={14} />
            </button>
            <button onClick={() => setActiveView("inventory")} className={`px-3 py-1.5 text-xs font-bold transition-all ${activeView === "inventory" ? "bg-[#FF6B35] text-white" : "text-gray-400 hover:text-white"}`}>
              <Package size={14} />
            </button>
          </div>

          <div className="text-gray-400 text-xs">
            {new Date().toLocaleTimeString("vi-VN", { hour: "2-digit", minute: "2-digit" })}
          </div>
        </div>
      </div>

      {/* Sync Queue Panel */}
      {showSyncPanel && (
        <div className="bg-[#1A1A1A] border-b-2 border-orange-500/30 px-6 py-4">
          <div className="flex items-center justify-between mb-3">
            <div className="flex items-center gap-2">
              <Database size={16} className="text-orange-400" />
              <span className="text-white font-black text-sm">Sync Queue (Hàng đợi đồng bộ)</span>
            </div>
            <button
              onClick={processSyncQueue}
              disabled={!isOnline || isSyncing}
              className="flex items-center gap-1.5 bg-orange-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg disabled:opacity-50"
            >
              {isSyncing ? <RefreshCw size={12} className="animate-spin" /> : <Upload size={12} />}
              {isSyncing ? "Đang sync..." : "Sync ngay"}
            </button>
          </div>
          <div className="space-y-2 max-h-40 overflow-y-auto">
            {syncQueue.slice().reverse().map((item) => (
              <div key={item.id} className={`flex items-center gap-3 text-xs rounded-lg px-3 py-2 border ${item.synced ? "bg-green-900/20 border-green-700/30 text-green-400" : "bg-orange-900/20 border-orange-700/30 text-orange-300"}`}>
                {item.synced ? <Check size={12} className="text-green-400" /> : <RefreshCw size={12} className="text-orange-400 animate-spin" />}
                <span className="font-bold">{item.orderId}</span>
                <span className="text-gray-400">→</span>
                <span>{item.action}</span>
                <span className="ml-auto text-gray-500">{new Date(item.timestamp).toLocaleTimeString("vi-VN")}</span>
              </div>
            ))}
          </div>
          <p className="text-gray-500 text-[10px] mt-2">
            * Dữ liệu được lưu tạm vào IndexedDB khi offline, tự động sync khi có kết nối
          </p>
        </div>
      )}

      {/* Main content */}
      {activeView === "kanban" ? (
        <div className="flex-1 flex gap-0 overflow-hidden">
          {/* TO-DO Column */}
          <KanbanColumn
            title="Cần Làm"
            count={todoOrders.length}
            color="bg-green-500"
            glowColor="border-green-500"
            orders={todoOrders}
            onAction={(id) => moveOrder(id, "cooking")}
            actionLabel="Bắt đầu nấu"
            actionColor="bg-green-500 hover:bg-green-600"
            getElapsedColor={getElapsedColor}
            getCardBorderColor={getCardBorderColor}
            isOnline={isOnline}
          />
          {/* COOKING Column */}
          <KanbanColumn
            title="Đang Nấu"
            count={cookingOrders.length}
            color="bg-yellow-500"
            glowColor="border-yellow-500"
            orders={cookingOrders}
            onAction={(id) => moveOrder(id, "ready")}
            actionLabel="Xong! Giao bàn"
            actionColor="bg-yellow-500 hover:bg-yellow-600 text-black"
            getElapsedColor={getElapsedColor}
            getCardBorderColor={getCardBorderColor}
            isOnline={isOnline}
          />
          {/* READY Column */}
          <KanbanColumn
            title="Sẵn Sàng"
            count={readyOrders.length}
            color="bg-blue-500"
            glowColor="border-blue-500"
            orders={readyOrders}
            onAction={(id) => moveOrder(id, "done")}
            actionLabel="Đã giao xong ✓"
            actionColor="bg-blue-500 hover:bg-blue-600"
            getElapsedColor={getElapsedColor}
            getCardBorderColor={getCardBorderColor}
            isOnline={isOnline}
          />
        </div>
      ) : (
        /* INVENTORY VIEW */
        <div className="flex-1 overflow-y-auto p-6">
          <div className="max-w-2xl mx-auto">
            <div className="mb-4 flex items-center justify-between">
              <h2 className="text-white font-black text-xl flex items-center gap-2">
                <Package size={20} className="text-[#FF6B35]" /> Quản lý Nguyên liệu
              </h2>
              <span className="text-gray-400 text-xs bg-[#1A1A1A] border border-[#333] px-3 py-1 rounded-lg">
                Toggle để đánh dấu HẾT HÀNG
              </span>
            </div>
            <div className="space-y-3">
              {inventory.map((item) => (
                <div key={item.id} className={`bg-[#1A1A1A] border-2 rounded-2xl p-4 transition-all ${!item.isAvailable ? "border-red-500 opacity-60" : item.status === "critical" ? "border-red-400" : item.status === "low" ? "border-yellow-400" : "border-[#333]"}`}>
                  <div className="flex items-center gap-4">
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-1">
                        <span className="text-white font-black">{item.name}</span>
                        {item.status === "critical" && <span className="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full animate-pulse">HẾT GẦN</span>}
                        {item.status === "low" && <span className="bg-yellow-500 text-black text-[10px] font-black px-2 py-0.5 rounded-full">THẤP</span>}
                        {!item.isAvailable && <span className="bg-gray-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full">HẾT HÀNG</span>}
                      </div>
                      <div className="flex items-center gap-3 text-xs text-gray-400">
                        <span>Còn: <span className="text-white font-bold">{item.current} {item.unit}</span></span>
                        <span>Ngưỡng an toàn: {item.safety} {item.unit}</span>
                      </div>
                      {/* Stock bar */}
                      <div className="mt-2 h-2 bg-[#333] rounded-full overflow-hidden">
                        <div
                          className={`h-full rounded-full transition-all ${item.status === "critical" ? "bg-red-500" : item.status === "low" ? "bg-yellow-500" : "bg-green-500"}`}
                          style={{ width: `${Math.min(100, (item.current / item.max) * 100)}%` }}
                        />
                      </div>
                    </div>
                    {/* Big toggle switch */}
                    <button
                      onClick={() => toggleInventory(item.id)}
                      className={`w-16 h-8 rounded-full border-2 transition-all flex items-center ${item.isAvailable ? "bg-green-500 border-green-500 justify-end" : "bg-gray-700 border-gray-600 justify-start"}`}
                    >
                      <div className="w-7 h-7 bg-white rounded-full shadow-md mx-0.5" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

function KanbanColumn({
  title, count, color, glowColor, orders, onAction, actionLabel, actionColor, getElapsedColor, getCardBorderColor, isOnline
}: {
  title: string; count: number; color: string; glowColor: string;
  orders: KDSOrder[]; onAction: (id: string) => void;
  actionLabel: string; actionColor: string;
  getElapsedColor: (e: number) => string;
  getCardBorderColor: (o: KDSOrder) => string;
  isOnline: boolean;
}) {
  return (
    <div className={`flex-1 flex flex-col border-r border-[#222] min-w-0`}>
      <div className={`px-4 py-3 border-b-2 border-[#333] flex items-center justify-between flex-shrink-0`}>
        <div className="flex items-center gap-2">
          <div className={`w-3 h-3 rounded-full ${color}`} />
          <span className="text-white font-black text-sm uppercase tracking-wide">{title}</span>
        </div>
        <span className={`${color} text-white text-xs font-black w-6 h-6 rounded-full flex items-center justify-center`}>{count}</span>
      </div>
      <div className="flex-1 overflow-y-auto p-3 space-y-3">
        {orders.length === 0 && (
          <div className="text-center py-8 text-gray-600 text-sm">
            <ChefHat size={32} className="mx-auto mb-2 opacity-30" />
            <p>Trống</p>
          </div>
        )}
        {orders.map((order) => (
          <div
            key={order.id}
            className={`bg-[#1A1A1A] border-2 rounded-2xl overflow-hidden transition-all ${getCardBorderColor(order)}`}
          >
            {/* Order header */}
            <div className="px-4 py-3 border-b border-[#333] flex items-center justify-between">
              <div>
                <div className="text-white font-black text-lg">{order.id}</div>
                <div className="text-gray-400 text-xs">{order.table}</div>
              </div>
              <div className="text-right">
                <div className={`font-black text-lg ${getElapsedColor(order.elapsed)}`}>
                  {order.elapsed}m
                </div>
                {order.priority === "high" && (
                  <div className="flex items-center gap-1 text-[#FF6B35]">
                    <Zap size={10} />
                    <span className="text-[10px] font-black">ƯU TIÊN</span>
                  </div>
                )}
                {order.elapsed > 20 && (
                  <div className="flex items-center gap-1 text-red-400">
                    <AlertTriangle size={10} />
                    <span className="text-[10px] font-black">TRỄ!</span>
                  </div>
                )}
              </div>
            </div>

            {/* Items */}
            <div className="px-4 py-3 space-y-2">
              {order.items.map((item, idx) => (
                <div key={idx} className="flex gap-3">
                  <div className="w-8 h-8 bg-[#FF6B35] rounded-xl flex items-center justify-center text-white font-black text-lg flex-shrink-0">
                    {item.qty}
                  </div>
                  <div className="flex-1">
                    <div className="text-white font-black text-base leading-tight">{item.name}</div>
                    {item.toppings.length > 0 && (
                      <div className="text-[#FFD23F] text-xs mt-0.5">+ {item.toppings.join(", ")}</div>
                    )}
                    {item.note && (
                      <div className="text-orange-400 text-xs font-bold mt-0.5">⚠️ {item.note}</div>
                    )}
                  </div>
                </div>
              ))}
            </div>

            {/* Action button */}
            <div className="px-4 pb-4">
              <button
                onClick={() => onAction(order.id)}
                className={`w-full py-3 rounded-xl font-black text-white text-base border-2 border-[#444] transition-all ${actionColor} ${!isOnline ? "opacity-80" : ""}`}
              >
                {actionLabel}
                {!isOnline && <span className="ml-1 text-[10px] opacity-70">(Offline queue)</span>}
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
