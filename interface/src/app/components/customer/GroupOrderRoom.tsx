import { useState, useEffect, useCallback, useRef } from "react";
import { useNavigate, useParams } from "react-router";
import { Users, Copy, Check, Plus, Minus, Lock, Zap, ChevronRight, UserCircle, Activity, Wifi, WifiOff, ShoppingBag, ArrowRight } from "lucide-react";
import { MENU_ITEMS, formatPrice } from "../../data/mockData";

interface Participant {
  id: string;
  name: string;
  emoji: string;
  isHost: boolean;
  isTyping: boolean;
  joinedAt: number;
}

interface OrderItem {
  menuItemId: string;
  quantity: number;
  note: string;
}

interface ParticipantOrder {
  participantId: string;
  items: OrderItem[];
}

interface RoomActivity {
  id: string;
  participantId: string;
  participantName: string;
  action: string;
  itemName: string;
  time: number;
  emoji: string;
}

const EMOJIS = ["😎", "🦁", "🐯", "🦊", "🐼", "🦄", "🐸", "🐙", "🦋", "🐲"];
const BOT_NAMES = ["Minh Tuấn", "Lan Anh", "Hoàng Nam", "Thu Hà", "Bảo Linh"];

function generateRoomCode() {
  const words = ["BANHMI", "PHOBO", "MITRON", "GARANG", "COMTAM"];
  const word = words[Math.floor(Math.random() * words.length)];
  const num = Math.floor(Math.random() * 900 + 100);
  return `${word}${num}`;
}

// ====== CREATE ROOM PAGE ======
export function CreateGroupOrder() {
  const navigate = useNavigate();
  const [yourName, setYourName] = useState("");
  const [roomCode] = useState(generateRoomCode);
  const [copied, setCopied] = useState(false);

  const handleCreate = () => {
    if (!yourName.trim()) return;
    const roomData = {
      code: roomCode,
      host: yourName,
      hostId: "host-" + Date.now(),
      createdAt: Date.now(),
      isLocked: false,
      participants: [
        { id: "host-" + Date.now(), name: yourName, emoji: "👑", isHost: true, isTyping: false, joinedAt: Date.now() }
      ],
      orders: [],
      activities: [],
    };
    localStorage.setItem(`room_${roomCode}`, JSON.stringify(roomData));
    localStorage.setItem("my_participant_id", roomData.participants[0].id);
    localStorage.setItem("my_name", yourName);
    navigate(`/group-order/${roomCode}`);
  };

  const copyLink = () => {
    navigator.clipboard.writeText(`${window.location.origin}/group-order/${roomCode}`);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="min-h-screen bg-[#FAFAF8] flex flex-col items-center justify-center p-4">
      <div className="w-full max-w-sm">
        {/* Header */}
        <div className="text-center mb-6">
          <div className="w-20 h-20 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] flex items-center justify-center mx-auto mb-3">
            <Users size={36} className="text-[#1C1C1C]" />
          </div>
          <h1 className="text-2xl font-black text-[#1C1C1C]">Đặt Đơn Nhóm</h1>
          <p className="text-sm text-gray-500 mt-1">Tạo phòng, mọi người tự chọn, chia bill tự động! 🎉</p>
        </div>

        {/* Form */}
        <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5 space-y-4">
          <div>
            <label className="text-xs font-black text-[#1C1C1C] mb-1 block uppercase tracking-wide">Tên của bạn</label>
            <input
              value={yourName}
              onChange={(e) => setYourName(e.target.value)}
              placeholder="Nhập tên để mọi người nhận ra..."
              className="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-[#FF6B35] focus:shadow-[2px_2px_0px_#FF6B35] transition-all"
              onKeyDown={(e) => e.key === "Enter" && handleCreate()}
            />
          </div>

          <div>
            <label className="text-xs font-black text-[#1C1C1C] mb-1 block uppercase tracking-wide">Mã phòng của bạn</label>
            <div className="flex gap-2">
              <div className="flex-1 bg-[#1C1C1C] text-[#FFD23F] font-black text-center py-3 rounded-xl text-lg tracking-widest border-2 border-[#1C1C1C]">
                {roomCode}
              </div>
              <button
                onClick={copyLink}
                className={`px-4 py-3 rounded-xl border-2 border-[#1C1C1C] font-bold text-sm flex items-center gap-1.5 transition-all shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] ${copied ? "bg-green-500 text-white" : "bg-[#FFD23F] text-[#1C1C1C]"}`}
              >
                {copied ? <Check size={16} /> : <Copy size={16} />}
                {copied ? "Đã copy!" : "Copy link"}
              </button>
            </div>
            <p className="text-xs text-gray-400 mt-1.5">Gửi link này cho đồng nghiệp vào chọn món cùng</p>
          </div>

          <button
            onClick={handleCreate}
            disabled={!yourName.trim()}
            className="w-full bg-[#FF6B35] text-white font-black py-3.5 rounded-xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] hover:shadow-[2px_2px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-base"
          >
            <Zap size={18} /> Tạo phòng ngay!
          </button>
        </div>

        {/* Join room */}
        <div className="mt-4 text-center">
          <p className="text-sm text-gray-500">Đã có mã phòng?</p>
          <button
            onClick={() => {
              const code = prompt("Nhập mã phòng:");
              if (code) navigate(`/group-order/${code.toUpperCase()}`);
            }}
            className="text-[#FF6B35] font-black text-sm underline mt-1"
          >
            Tham gia phòng có sẵn →
          </button>
        </div>
      </div>
    </div>
  );
}

// ====== GROUP ORDER ROOM ======
export function GroupOrderRoom() {
  const { roomId } = useParams<{ roomId: string }>();
  const navigate = useNavigate();
  const [myName, setMyName] = useState(localStorage.getItem("my_name") || "");
  const [myId] = useState(() => localStorage.getItem("my_participant_id") || "user-" + Date.now());
  const [joined, setJoined] = useState(!!localStorage.getItem("my_participant_id"));
  const [joinName, setJoinName] = useState("");
  const [participants, setParticipants] = useState<Participant[]>([]);
  const [orders, setOrders] = useState<ParticipantOrder[]>([]);
  const [activities, setActivities] = useState<RoomActivity[]>([]);
  const [isLocked, setIsLocked] = useState(false);
  const [isOnline, setIsOnline] = useState(navigator.onLine);
  const [selectedCategory, setSelectedCategory] = useState("all");
  const [activeTab, setActiveTab] = useState<"menu" | "orders" | "activity">("menu");
  const activityRef = useRef<HTMLDivElement>(null);
  // botTimerRef removed - using setTimeout directly in useEffect

  const isHost = participants.find((p) => p.id === myId)?.isHost ?? false;
  const myOrder = orders.find((o) => o.participantId === myId);
  const myItemCount = myOrder?.items.reduce((sum, i) => sum + i.quantity, 0) ?? 0;

  const saveRoom = useCallback((data: any) => {
    localStorage.setItem(`room_${roomId}`, JSON.stringify(data));
  }, [roomId]);

  const loadRoom = useCallback(() => {
    try {
      const raw = localStorage.getItem(`room_${roomId}`);
      if (!raw) return null;
      return JSON.parse(raw);
    } catch { return null; }
  }, [roomId]);

  // Initialize / sync room state
  useEffect(() => {
    const syncRoom = () => {
      const room = loadRoom();
      if (!room) return;
      setParticipants(room.participants || []);
      setOrders(room.orders || []);
      setActivities(room.activities || []);
      setIsLocked(room.isLocked || false);
    };
    syncRoom();
    const interval = setInterval(syncRoom, 1500); // Simulate WebSocket polling
    return () => clearInterval(interval);
  }, [loadRoom]);

  // Simulate bot participants joining and adding items
  useEffect(() => {
    if (!joined) return;
    const room = loadRoom();
    if (!room) return;

    // Bots join after 3s, 6s
    const botJoinTimers = BOT_NAMES.slice(0, 2).map((name, i) => {
      return setTimeout(() => {
        const room = loadRoom();
        if (!room || room.isLocked) return;
        const botId = `bot-${name}`;
        if (room.participants.find((p: any) => p.id === botId)) return;
        const bot: Participant = { id: botId, name, emoji: EMOJIS[i + 1], isHost: false, isTyping: false, joinedAt: Date.now() };
        room.participants.push(bot);
        addActivity(room, botId, name, "tham gia phòng 🎉", "", EMOJIS[i + 1]);
        saveRoom(room);
      }, (i + 1) * 3000);
    });

    // Bots add items after 7s, 10s, 13s
    const botOrderTimers = [
      { delay: 7000, botName: "Minh Tuấn", itemId: "1", itemName: "Mì trộn đặc biệt" },
      { delay: 10000, botName: "Lan Anh", itemId: "2", itemName: "Trà sữa trân châu đen" },
      { delay: 13000, botName: "Minh Tuấn", itemId: "5", itemName: "Sinh tố xoài nhiệt đới" },
    ].map(({ delay, botName, itemId, itemName }) =>
      setTimeout(() => {
        const room = loadRoom();
        if (!room || room.isLocked) return;
        const botId = `bot-${botName}`;
        if (!room.participants.find((p: any) => p.id === botId)) return;

        // Typing indicator
        const pIdx = room.participants.findIndex((p: any) => p.id === botId);
        if (pIdx >= 0) room.participants[pIdx].isTyping = true;
        saveRoom(room);

        setTimeout(() => {
          const room2 = loadRoom();
          if (!room2) return;
          const pIdx2 = room2.participants.findIndex((p: any) => p.id === botId);
          if (pIdx2 >= 0) room2.participants[pIdx2].isTyping = false;

          let oIdx = room2.orders.findIndex((o: any) => o.participantId === botId);
          if (oIdx < 0) {
            room2.orders.push({ participantId: botId, items: [] });
            oIdx = room2.orders.length - 1;
          }
          const existItem = room2.orders[oIdx].items.find((it: any) => it.menuItemId === itemId);
          if (existItem) existItem.quantity += 1;
          else room2.orders[oIdx].items.push({ menuItemId: itemId, quantity: 1, note: "" });

          addActivity(room2, botId, botName, "vừa thêm", itemName, "🛒");
          saveRoom(room2);
        }, 1500);
      }, delay)
    );

    return () => {
      botJoinTimers.forEach(clearTimeout);
      botOrderTimers.forEach(clearTimeout);
    };
  }, [joined, loadRoom, saveRoom]);

  // Network status
  useEffect(() => {
    const onOnline = () => setIsOnline(true);
    const onOffline = () => setIsOnline(false);
    window.addEventListener("online", onOnline);
    window.addEventListener("offline", onOffline);
    return () => { window.removeEventListener("online", onOnline); window.removeEventListener("offline", onOffline); };
  }, []);

  // Scroll activity to bottom
  useEffect(() => {
    if (activityRef.current) activityRef.current.scrollTop = activityRef.current.scrollHeight;
  }, [activities]);

  const addActivity = (room: any, participantId: string, name: string, action: string, itemName: string, emoji: string) => {
    const act: RoomActivity = { id: Date.now().toString(), participantId, participantName: name, action, itemName, time: Date.now(), emoji };
    room.activities = [...(room.activities || []).slice(-50), act];
  };

  const handleJoin = () => {
    if (!joinName.trim()) return;
    const room = loadRoom() || { code: roomId, participants: [], orders: [], activities: [], isLocked: false };
    const newId = "user-" + Date.now();
    const participant: Participant = { id: newId, name: joinName, emoji: EMOJIS[Math.floor(Math.random() * EMOJIS.length)], isHost: false, isTyping: false, joinedAt: Date.now() };
    room.participants.push(participant);
    addActivity(room, newId, joinName, "tham gia phòng 🎉", "", "🎉");
    saveRoom(room);
    localStorage.setItem("my_participant_id", newId);
    localStorage.setItem("my_name", joinName);
    setMyName(joinName);
    setJoined(true);
  };

  const updateMyItem = (menuItemId: string, delta: number) => {
    const room = loadRoom();
    if (!room || room.isLocked) return;

    let oIdx = room.orders.findIndex((o: any) => o.participantId === myId);
    if (oIdx < 0) { room.orders.push({ participantId: myId, items: [] }); oIdx = room.orders.length - 1; }

    const itemIdx = room.orders[oIdx].items.findIndex((it: any) => it.menuItemId === menuItemId);
    const menuItem = MENU_ITEMS.find((m) => m.id === menuItemId);
    const myOrderName = participants.find((p) => p.id === myId)?.name ?? myName;

    if (delta > 0) {
      if (itemIdx < 0) room.orders[oIdx].items.push({ menuItemId, quantity: 1, note: "" });
      else room.orders[oIdx].items[itemIdx].quantity += 1;
      if (menuItem) addActivity(room, myId, myOrderName, "vừa thêm", menuItem.name, "🛒");
    } else {
      if (itemIdx >= 0) {
        room.orders[oIdx].items[itemIdx].quantity = Math.max(0, room.orders[oIdx].items[itemIdx].quantity - 1);
        if (room.orders[oIdx].items[itemIdx].quantity === 0) room.orders[oIdx].items.splice(itemIdx, 1);
        if (menuItem) addActivity(room, myId, myOrderName, "bỏ bớt", menuItem.name, "❌");
      }
    }
    saveRoom(room);
  };

  const getMyItemQty = (menuItemId: string) => {
    return myOrder?.items.find((i) => i.menuItemId === menuItemId)?.quantity ?? 0;
  };

  const lockRoom = () => {
    const room = loadRoom();
    if (!room) return;
    room.isLocked = true;
    addActivity(room, myId, myName, "đã khoá đơn hàng! 🔒", "", "🔒");
    saveRoom(room);
    navigate(`/split-bill/${roomId}`);
  };

  const getTotalByParticipant = (participantId: string) => {
    const order = orders.find((o) => o.participantId === participantId);
    if (!order) return 0;
    return order.items.reduce((sum, it) => {
      const menu = MENU_ITEMS.find((m) => m.id === it.menuItemId);
      return sum + (menu?.price ?? 0) * it.quantity;
    }, 0);
  };

  const categories = [
    { id: "all", label: "Tất cả", emoji: "🍽️" },
    { id: "noodles", label: "Mì & Phở", emoji: "🍜" },
    { id: "rice", label: "Cơm", emoji: "🍚" },
    { id: "snacks", label: "Ăn vặt", emoji: "🍗" },
    { id: "drinks", label: "Đồ uống", emoji: "🧋" },
  ];

  const filteredMenu = MENU_ITEMS.filter((item) => selectedCategory === "all" || item.category === selectedCategory);

  // JOIN FORM
  if (!joined) {
    return (
      <div className="min-h-screen bg-[#FAFAF8] flex items-center justify-center p-4">
        <div className="w-full max-w-sm">
          <div className="text-center mb-6">
            <div className="w-16 h-16 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] flex items-center justify-center mx-auto mb-3">
              <Users size={28} className="text-[#1C1C1C]" />
            </div>
            <h1 className="text-xl font-black text-[#1C1C1C]">Tham gia phòng</h1>
            <p className="text-sm text-gray-500">Mã phòng: <span className="font-black text-[#FF6B35]">{roomId}</span></p>
          </div>
          <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-5 space-y-4">
            <input
              value={joinName}
              onChange={(e) => setJoinName(e.target.value)}
              placeholder="Nhập tên của bạn..."
              className="w-full border-2 border-[#1C1C1C] rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-[#FF6B35] transition-all"
              onKeyDown={(e) => e.key === "Enter" && handleJoin()}
            />
            <button
              onClick={handleJoin}
              disabled={!joinName.trim()}
              className="w-full bg-[#FF6B35] text-white font-black py-3 rounded-xl border-2 border-[#1C1C1C] shadow-[3px_3px_0px_#1C1C1C] disabled:opacity-50 flex items-center justify-center gap-2"
            >
              <Zap size={16} /> Vào phòng đặt món!
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#FAFAF8] flex flex-col max-w-[430px] mx-auto">
      {/* Header */}
      <div className="sticky top-0 z-30 bg-white border-b-2 border-[#1C1C1C] px-4 py-3">
        <div className="flex items-center justify-between mb-2">
          <div>
            <h1 className="font-black text-[#1C1C1C] flex items-center gap-2">
              <Users size={16} className="text-[#FF6B35]" />
              Phòng #{roomId}
              {isLocked && <Lock size={14} className="text-red-500" />}
            </h1>
            <div className="flex items-center gap-1.5 mt-0.5">
              {isOnline ? (
                <><Wifi size={10} className="text-green-500" /><span className="text-[10px] text-green-600 font-bold">Đồng bộ thời gian thực</span></>
              ) : (
                <><WifiOff size={10} className="text-red-500" /><span className="text-[10px] text-red-600 font-bold">Mất kết nối</span></>
              )}
            </div>
          </div>
          <div className="flex items-center gap-2">
            {/* Participants avatars */}
            <div className="flex -space-x-2">
              {participants.slice(0, 4).map((p) => (
                <div key={p.id} className={`w-7 h-7 rounded-full border-2 border-white bg-[#FFD23F] flex items-center justify-center text-xs ${p.isTyping ? "ring-2 ring-[#FF6B35] ring-offset-1" : ""}`} title={p.name}>
                  {p.isTyping ? "✍️" : p.emoji}
                </div>
              ))}
            </div>
            <span className="text-xs text-gray-500 font-bold">{participants.length} người</span>
          </div>
        </div>

        {/* My order badge */}
        <div className="flex items-center gap-2">
          <div className="flex-1 bg-[#1C1C1C] text-[#FFD23F] text-xs font-bold px-3 py-1.5 rounded-xl flex items-center gap-1.5">
            <ShoppingBag size={12} />
            Của tôi: <span className="text-white">{myItemCount} món</span>
            <span className="ml-auto">{formatPrice(getTotalByParticipant(myId))}</span>
          </div>
          {isHost && (
            <button
              onClick={lockRoom}
              className="bg-[#FF6B35] text-white text-xs font-black px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] flex items-center gap-1 hover:shadow-[1px_1px_0px_#1C1C1C] transition-all"
            >
              <Lock size={12} /> Chốt đơn
            </button>
          )}
        </div>
      </div>

      {/* Typing indicators */}
      {participants.some((p) => p.isTyping && p.id !== myId) && (
        <div className="bg-[#FFD23F]/30 border-b border-[#FFD23F] px-4 py-2">
          <p className="text-xs text-[#1C1C1C] font-bold flex items-center gap-1.5">
            <span className="flex gap-0.5">
              <span className="w-1.5 h-1.5 bg-[#FF6B35] rounded-full animate-bounce" style={{ animationDelay: "0ms" }} />
              <span className="w-1.5 h-1.5 bg-[#FF6B35] rounded-full animate-bounce" style={{ animationDelay: "150ms" }} />
              <span className="w-1.5 h-1.5 bg-[#FF6B35] rounded-full animate-bounce" style={{ animationDelay: "300ms" }} />
            </span>
            {participants.find((p) => p.isTyping && p.id !== myId)?.name} đang chọn món...
          </p>
        </div>
      )}

      {/* Tabs */}
      <div className="flex border-b-2 border-[#1C1C1C] bg-white">
        {(["menu", "orders", "activity"] as const).map((tab) => (
          <button
            key={tab}
            onClick={() => setActiveTab(tab)}
            className={`flex-1 py-2.5 text-xs font-black uppercase tracking-wide border-r last:border-r-0 border-[#1C1C1C] transition-all ${activeTab === tab ? "bg-[#FF6B35] text-white" : "text-gray-500 hover:bg-gray-50"}`}
          >
            {tab === "menu" ? "🍽️ Chọn món" : tab === "orders" ? `👥 Đơn nhóm` : "⚡ Hoạt động"}
          </button>
        ))}
      </div>

      <div className="flex-1 overflow-y-auto">
        {/* MENU TAB */}
        {activeTab === "menu" && (
          <div className="p-4">
            {isLocked && (
              <div className="bg-red-50 border-2 border-red-300 rounded-xl p-3 mb-4 flex items-center gap-2">
                <Lock size={16} className="text-red-500" />
                <span className="text-sm font-bold text-red-600">Đơn đã bị khoá. Không thể thêm món.</span>
              </div>
            )}
            {/* Category filter */}
            <div className="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
              {categories.map((cat) => (
                <button
                  key={cat.id}
                  onClick={() => setSelectedCategory(cat.id)}
                  className={`flex-shrink-0 text-xs font-bold px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] transition-all ${selectedCategory === cat.id ? "bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]" : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"}`}
                >
                  {cat.emoji} {cat.label}
                </button>
              ))}
            </div>

            <div className="space-y-3">
              {filteredMenu.map((item) => {
                const qty = getMyItemQty(item.id);
                return (
                  <div key={item.id} className={`bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden flex ${qty > 0 ? "border-[#FF6B35] shadow-[3px_3px_0px_#FF6B35]" : ""}`}>
                    <img src={item.image} alt={item.name} className="w-24 h-24 object-cover flex-shrink-0" />
                    <div className="flex-1 p-3 flex flex-col justify-between">
                      <div>
                        <div className="font-black text-[#1C1C1C] text-sm leading-tight">{item.name}</div>
                        <div className="text-[#FF6B35] font-black text-sm mt-0.5">{formatPrice(item.price)}</div>
                      </div>
                      <div className="flex items-center gap-2 mt-2">
                        {qty > 0 ? (
                          <div className="flex items-center gap-2">
                            <button onClick={() => updateMyItem(item.id, -1)} className="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C] hover:shadow-none transition-all">
                              <Minus size={12} />
                            </button>
                            <span className="font-black text-[#1C1C1C] min-w-[16px] text-center">{qty}</span>
                            <button onClick={() => updateMyItem(item.id, 1)} className="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C] hover:shadow-none transition-all">
                              <Plus size={12} />
                            </button>
                          </div>
                        ) : (
                          <button
                            onClick={() => updateMyItem(item.id, 1)}
                            disabled={isLocked}
                            className="flex items-center gap-1.5 bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] disabled:opacity-50 transition-all"
                          >
                            <Plus size={12} /> Thêm vào
                          </button>
                        )}
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )}

        {/* ORDERS TAB */}
        {activeTab === "orders" && (
          <div className="p-4 space-y-3">
            {participants.map((p) => {
              const order = orders.find((o) => o.participantId === p.id);
              const total = getTotalByParticipant(p.id);
              return (
                <div key={p.id} className={`bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-4 ${p.id === myId ? "border-[#FF6B35] shadow-[3px_3px_0px_#FF6B35]" : ""}`}>
                  <div className="flex items-center gap-2 mb-3">
                    <div className="w-8 h-8 bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-full flex items-center justify-center text-sm">{p.emoji}</div>
                    <div className="flex-1">
                      <div className="font-black text-[#1C1C1C] text-sm flex items-center gap-1.5">
                        {p.name}
                        {p.id === myId && <span className="text-[#FF6B35] text-[10px]">(Bạn)</span>}
                        {p.isHost && <span className="bg-[#FFD23F] text-[#1C1C1C] text-[9px] font-black px-1.5 py-0.5 rounded-full border border-[#1C1C1C]">Host</span>}
                        {p.isTyping && <span className="text-orange-500 text-[10px] animate-pulse">đang chọn...</span>}
                      </div>
                    </div>
                    <span className="font-black text-[#FF6B35]">{formatPrice(total)}</span>
                  </div>
                  {order && order.items.length > 0 ? (
                    <div className="space-y-1.5">
                      {order.items.map((it) => {
                        const menu = MENU_ITEMS.find((m) => m.id === it.menuItemId);
                        if (!menu) return null;
                        return (
                          <div key={it.menuItemId} className="flex items-center justify-between text-xs">
                            <span className="text-gray-600">{menu.name}</span>
                            <span className="font-bold text-[#1C1C1C]">x{it.quantity} · {formatPrice(menu.price * it.quantity)}</span>
                          </div>
                        );
                      })}
                    </div>
                  ) : (
                    <p className="text-xs text-gray-400 italic">Chưa chọn món nào...</p>
                  )}
                </div>
              );
            })}

            {/* Grand total */}
            <div className="bg-[#1C1C1C] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#FF6B35] p-4">
              <div className="flex items-center justify-between">
                <span className="text-white font-black">Tổng đơn nhóm</span>
                <span className="text-[#FFD23F] font-black text-lg">
                  {formatPrice(participants.reduce((sum, p) => sum + getTotalByParticipant(p.id), 0))}
                </span>
              </div>
              {isHost && (
                <button onClick={lockRoom} className="w-full mt-3 bg-[#FF6B35] text-white font-black py-3 rounded-xl border-2 border-[#FF6B35] flex items-center justify-center gap-2">
                  <Lock size={16} /> Chốt đơn & Chia bill <ArrowRight size={16} />
                </button>
              )}
            </div>
          </div>
        )}

        {/* ACTIVITY TAB */}
        {activeTab === "activity" && (
          <div className="p-4">
            <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] overflow-hidden">
              <div className="bg-[#1C1C1C] px-4 py-2.5 flex items-center gap-2">
                <Activity size={14} className="text-[#FFD23F]" />
                <span className="text-white text-xs font-black uppercase tracking-wide">Live Activity Feed</span>
                <div className="ml-auto flex items-center gap-1">
                  <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse" />
                  <span className="text-green-400 text-[10px]">LIVE</span>
                </div>
              </div>
              <div ref={activityRef} className="h-96 overflow-y-auto p-3 space-y-2">
                {activities.length === 0 ? (
                  <p className="text-center text-gray-400 text-xs py-8">Chưa có hoạt động nào...</p>
                ) : (
                  activities.map((act) => (
                    <div key={act.id} className="flex items-start gap-2 animate-in slide-in-from-bottom-2">
                      <span className="text-lg flex-shrink-0">{act.emoji}</span>
                      <div className="flex-1 bg-gray-50 rounded-xl px-3 py-2">
                        <span className="font-bold text-[#1C1C1C] text-xs">{act.participantName}</span>
                        <span className="text-gray-500 text-xs"> {act.action} </span>
                        {act.itemName && <span className="font-bold text-[#FF6B35] text-xs">"{act.itemName}"</span>}
                      </div>
                      <span className="text-[10px] text-gray-400 flex-shrink-0 mt-1">
                        {new Date(act.time).toLocaleTimeString("vi-VN", { hour: "2-digit", minute: "2-digit", second: "2-digit" })}
                      </span>
                    </div>
                  ))
                )}
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}