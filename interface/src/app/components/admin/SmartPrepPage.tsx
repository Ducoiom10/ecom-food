import { useState, useEffect } from "react";
import { Brain, Cloud, CloudRain, Sun, Thermometer, TrendingUp, TrendingDown, AlertTriangle, Bell, Check, RefreshCw, ChevronRight, Zap, Clock, Users, Package } from "lucide-react";
import { AreaChart, Area, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from "recharts";
import { HISTORICAL_SALES, MENU_ITEMS, formatPrice } from "../../data/mockData";

interface PrepRecommendation {
  id: string;
  item: string;
  emoji: string;
  predicted: number;
  prepped: number;
  unit: string;
  urgency: "low" | "medium" | "high" | "critical";
  reason: string;
  action: string;
  acknowledged: boolean;
}

interface WeatherData {
  condition: "sunny" | "cloudy" | "rainy" | "stormy";
  temp: number;
  impact: string;
  deliveryBoost: number;
}

const WEATHER_CONDITIONS: WeatherData[] = [
  { condition: "sunny", temp: 34, impact: "Nắng nóng → Đơn đồ uống tăng 40%", deliveryBoost: 0 },
  { condition: "cloudy", temp: 28, impact: "Trời mát → Đơn bình thường", deliveryBoost: 10 },
  { condition: "rainy", temp: 24, impact: "Mưa lớn → Đơn ship tăng vọt 85%! Bếp cần thêm người", deliveryBoost: 85 },
  { condition: "stormy", temp: 22, impact: "Bão/Giông → Đơn ship cực cao, cần chuẩn bị hộp giấy thêm!", deliveryBoost: 120 },
];

function getCurrentMealPeriod() {
  const hour = new Date().getHours();
  if (hour >= 6 && hour < 9) return { name: "Bữa sáng", emoji: "🌅", peak: false };
  if (hour >= 9 && hour < 11) return { name: "Giữa sáng", emoji: "☕", peak: false };
  if (hour >= 11 && hour < 14) return { name: "Bữa trưa", emoji: "☀️", peak: true };
  if (hour >= 14 && hour < 17) return { name: "Chiều tối", emoji: "🌤️", peak: false };
  if (hour >= 17 && hour < 21) return { name: "Bữa tối", emoji: "🌆", peak: true };
  return { name: "Đêm khuya", emoji: "🌙", peak: false };
}

function generateRecommendations(weather: WeatherData, hour: number): PrepRecommendation[] {
  const isLunchPeak = hour >= 11 && hour < 13;
  const isDinnerPeak = hour >= 17 && hour < 19;
  const isRainy = weather.condition === "rainy" || weather.condition === "stormy";

  const recs: PrepRecommendation[] = [
    {
      id: "r1",
      item: "Mì trứng (nguyên liệu Mì trộn)",
      emoji: "🍜",
      predicted: isLunchPeak ? 80 : isDinnerPeak ? 65 : 30,
      prepped: 45,
      unit: "vắt",
      urgency: isLunchPeak ? "critical" : "medium",
      reason: isLunchPeak ? "Bắt đầu giờ trưa cao điểm! Dự báo 80 vắt mì được bán" : "Dự báo dựa trên lịch sử 7 ngày qua",
      action: `Luộc sẵn ${isLunchPeak ? 35 : 15} vắt mì ngay!`,
      acknowledged: false,
    },
    {
      id: "r2",
      item: "Nước dùng Phở",
      emoji: "🍲",
      predicted: isLunchPeak ? 60 : 30,
      prepped: 40,
      unit: "lít",
      urgency: isLunchPeak ? "high" : "low",
      reason: "Phở nấu cần 12h, phải chuẩn bị từ tối hôm qua",
      action: "Kiểm tra nồi nước dùng, bổ sung xương nếu thiếu",
      acknowledged: false,
    },
    {
      id: "r3",
      item: "Hộp đựng thức ăn (Ship)",
      emoji: "📦",
      predicted: isRainy ? 200 : 80,
      prepped: 120,
      unit: "hộp",
      urgency: isRainy ? "critical" : "low",
      reason: isRainy ? `Mưa to → Đơn ship tăng ${weather.deliveryBoost}%! Cần gấp đôi hộp giấy!` : "Mức bình thường",
      action: isRainy ? "Lấy thêm hộp từ kho dự phòng NGAY!" : "Kiểm tra lại tồn kho",
      acknowledged: false,
    },
    {
      id: "r4",
      item: "Trân châu đen (Trà sữa)",
      emoji: "🧋",
      predicted: isLunchPeak || isDinnerPeak ? 50 : 20,
      prepped: 35,
      unit: "kg",
      urgency: isLunchPeak ? "medium" : "low",
      reason: "Trân châu cần ngâm 30 phút trước khi dùng",
      action: "Bắt đầu ngâm 15kg trân châu cho ca chiều",
      acknowledged: false,
    },
    {
      id: "r5",
      item: "Nhân viên bổ sung",
      emoji: "👨‍🍳",
      predicted: isRainy ? 4 : 2,
      prepped: 2,
      unit: "người",
      urgency: isRainy ? "high" : "low",
      reason: isRainy ? "Mưa to dự báo đơn tăng gấp 3, cần thêm nhân viên bếp" : "Đủ nhân sự",
      action: isRainy ? "Gọi 2 nhân viên part-time vào ca NGAY" : "Giữ nguyên",
      acknowledged: false,
    },
  ];

  return recs;
}

const WEATHER_ICONS = {
  sunny: <Sun size={32} className="text-yellow-400" />,
  cloudy: <Cloud size={32} className="text-gray-400" />,
  rainy: <CloudRain size={32} className="text-blue-400" />,
  stormy: <CloudRain size={32} className="text-purple-400" />,
};

export function SmartPrepPage() {
  const [currentWeatherIdx, setCurrentWeatherIdx] = useState(2); // Default rainy for demo
  const [recommendations, setRecommendations] = useState<PrepRecommendation[]>([]);
  const [hour] = useState(new Date().getHours() === 0 ? 12 : new Date().getHours()); // Default to noon if midnight
  const [acknowledged, setAcknowledged] = useState<Set<string>>(new Set());
  const [isLoadingAI, setIsLoadingAI] = useState(false);
  const [lastUpdated, setLastUpdated] = useState(new Date());
  const [showNotif, setShowNotif] = useState(true);
  const [selectedPeriod, setSelectedPeriod] = useState("today");

  const weather = WEATHER_CONDITIONS[currentWeatherIdx];
  const mealPeriod = getCurrentMealPeriod();

  useEffect(() => {
    setRecommendations(generateRecommendations(weather, hour));
  }, [currentWeatherIdx, hour]);

  const refreshAI = () => {
    setIsLoadingAI(true);
    setTimeout(() => {
      setRecommendations(generateRecommendations(weather, hour));
      setLastUpdated(new Date());
      setIsLoadingAI(false);
    }, 2000);
  };

  const acknowledgeRec = (id: string) => {
    setAcknowledged((prev) => {
      const next = new Set(prev);
      next.add(id);
      return next;
    });
  };

  const getUrgencyConfig = (urgency: PrepRecommendation["urgency"]) => {
    const configs = {
      critical: { bg: "bg-red-900/30", border: "border-red-500", badge: "bg-red-500 text-white", label: "KHẨN CẤP", dot: "bg-red-500 animate-pulse" },
      high: { bg: "bg-orange-900/30", border: "border-orange-500", badge: "bg-orange-500 text-white", label: "CAO", dot: "bg-orange-500" },
      medium: { bg: "bg-yellow-900/20", border: "border-yellow-500", badge: "bg-yellow-500 text-black", label: "TRUNG BÌNH", dot: "bg-yellow-500" },
      low: { bg: "bg-[#1A1A1A]", border: "border-[#333]", badge: "bg-gray-600 text-white", label: "THẤP", dot: "bg-gray-500" },
    };
    return configs[urgency];
  };

  const criticalCount = recommendations.filter((r) => r.urgency === "critical" && !acknowledged.has(r.id)).length;
  const highCount = recommendations.filter((r) => r.urgency === "high" && !acknowledged.has(r.id)).length;

  // Calculate prediction data for chart
  const predictionData = HISTORICAL_SALES.slice(0, 10).map((d) => ({
    ...d,
    predictedTotal: Math.round(d.total * (weather.condition === "rainy" ? 1.85 : weather.condition === "stormy" ? 2.2 : 1.1)),
  }));

  return (
    <div className="h-full overflow-y-auto bg-[#0F0F0F] p-6">
      {/* Alert Banner */}
      {showNotif && (criticalCount > 0 || highCount > 0) && (
        <div className="bg-red-600 border-2 border-red-400 rounded-2xl p-4 mb-6 flex items-center gap-3">
          <div className="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <Bell size={20} className="text-white" />
          </div>
          <div className="flex-1">
            <p className="text-white font-black">🚨 {criticalCount} cảnh báo KHẨN CẤP, {highCount} cảnh báo CAO!</p>
            <p className="text-red-200 text-xs mt-0.5">Hệ thống AI phát hiện cần chuẩn bị gấp trước giờ cao điểm</p>
          </div>
          <button onClick={() => setShowNotif(false)} className="text-white/70 hover:text-white">✕</button>
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left: Weather & AI Status */}
        <div className="space-y-4">
          {/* Weather Widget */}
          <div className="bg-gradient-to-br from-[#1A2040] to-[#0D1520] border-2 border-[#2A3A6A] rounded-2xl p-5">
            <div className="flex items-center gap-2 mb-4">
              <Brain size={16} className="text-blue-400" />
              <span className="text-blue-300 text-xs font-black uppercase tracking-wide">Dữ liệu thời tiết</span>
            </div>
            <div className="flex items-center gap-4 mb-4">
              {WEATHER_ICONS[weather.condition]}
              <div>
                <div className="text-white font-black text-3xl">{weather.temp}°C</div>
                <div className="text-gray-400 text-sm capitalize">{weather.condition === "sunny" ? "Nắng" : weather.condition === "cloudy" ? "Nhiều mây" : weather.condition === "rainy" ? "Mưa lớn" : "Mưa bão"}</div>
              </div>
            </div>
            <div className={`rounded-xl p-3 text-xs font-bold ${weather.condition === "rainy" || weather.condition === "stormy" ? "bg-blue-900/50 text-blue-300 border border-blue-700/50" : "bg-yellow-900/30 text-yellow-300 border border-yellow-700/30"}`}>
              {weather.impact}
            </div>
            {weather.deliveryBoost > 0 && (
              <div className="mt-3 flex items-center gap-2 bg-green-900/30 border border-green-700/30 rounded-xl p-3">
                <TrendingUp size={16} className="text-green-400 flex-shrink-0" />
                <div>
                  <div className="text-green-400 font-black text-sm">Đơn Ship +{weather.deliveryBoost}%</div>
                  <div className="text-green-300/70 text-[10px]">So với ngày nắng bình thường</div>
                </div>
              </div>
            )}
            {/* Simulate different weather */}
            <div className="mt-3">
              <p className="text-gray-500 text-[10px] mb-1.5">Mô phỏng thời tiết:</p>
              <div className="flex gap-1">
                {WEATHER_CONDITIONS.map((w, i) => (
                  <button
                    key={i}
                    onClick={() => setCurrentWeatherIdx(i)}
                    className={`flex-1 py-1 rounded-lg text-sm transition-all ${currentWeatherIdx === i ? "bg-blue-500 border border-blue-400" : "bg-[#252525] border border-[#333] hover:border-blue-500/50"}`}
                  >
                    {WEATHER_ICONS[w.condition]}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* Meal period */}
          <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
            <div className="flex items-center gap-2 mb-3">
              <Clock size={16} className="text-[#FF6B35]" />
              <span className="text-white font-black text-sm">Ca hiện tại</span>
            </div>
            <div className={`flex items-center gap-3 ${mealPeriod.peak ? "text-[#FFD23F]" : "text-gray-300"}`}>
              <span className="text-3xl">{mealPeriod.emoji}</span>
              <div>
                <div className="font-black text-lg">{mealPeriod.name}</div>
                {mealPeriod.peak && (
                  <div className="flex items-center gap-1 text-xs">
                    <Zap size={10} className="text-[#FF6B35]" />
                    <span className="text-[#FF6B35] font-bold">GIỜ CAO ĐIỂM!</span>
                  </div>
                )}
              </div>
            </div>
            <div className="mt-3 text-xs text-gray-400">
              Thời điểm: {new Date().toLocaleTimeString("vi-VN", { hour: "2-digit", minute: "2-digit" })}
            </div>
          </div>

          {/* Stats */}
          <div className="grid grid-cols-2 gap-3">
            <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4 text-center">
              <div className="text-[#FFD23F] font-black text-2xl">
                {recommendations.filter((r) => !acknowledged.has(r.id)).length}
              </div>
              <div className="text-gray-400 text-xs mt-1">Gợi ý chờ</div>
            </div>
            <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4 text-center">
              <div className="text-green-400 font-black text-2xl">{acknowledged.size}</div>
              <div className="text-gray-400 text-xs mt-1">Đã xử lý</div>
            </div>
          </div>

          {/* AI refresh */}
          <button
            onClick={refreshAI}
            disabled={isLoadingAI}
            className="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-black py-3 rounded-2xl border-2 border-purple-500 disabled:opacity-70 hover:from-purple-500 hover:to-blue-500 transition-all"
          >
            {isLoadingAI ? <RefreshCw size={16} className="animate-spin" /> : <Brain size={16} />}
            {isLoadingAI ? "AI đang phân tích..." : "Cập nhật dự báo AI"}
          </button>
          <p className="text-gray-600 text-[10px] text-center">
            Cập nhật lúc: {lastUpdated.toLocaleTimeString("vi-VN")}
          </p>
        </div>

        {/* Center: Recommendations */}
        <div className="lg:col-span-2 space-y-4">
          <div className="flex items-center justify-between">
            <h2 className="text-white font-black text-xl flex items-center gap-2">
              <Brain size={20} className="text-purple-400" />
              Gợi ý chuẩn bị từ AI
            </h2>
            <span className="text-gray-500 text-xs bg-[#1A1A1A] border border-[#333] px-3 py-1.5 rounded-lg">
              Dựa trên lịch sử 30 ngày + thời tiết thực tế
            </span>
          </div>

          {recommendations.map((rec) => {
            const cfg = getUrgencyConfig(rec.urgency);
            const isAck = acknowledged.has(rec.id);
            const progress = Math.min(100, (rec.prepped / rec.predicted) * 100);
            return (
              <div
                key={rec.id}
                className={`border-2 rounded-2xl overflow-hidden transition-all ${isAck ? "opacity-50 border-[#333] bg-[#111]" : `${cfg.border} ${cfg.bg}`}`}
              >
                <div className="p-4">
                  <div className="flex items-start gap-3 mb-3">
                    <span className="text-3xl flex-shrink-0">{rec.emoji}</span>
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center gap-2 mb-1 flex-wrap">
                        <span className="text-white font-black">{rec.item}</span>
                        <span className={`text-[10px] font-black px-2 py-0.5 rounded-full ${cfg.badge}`}>{cfg.label}</span>
                        {isAck && <span className="text-green-400 text-[10px] font-black">✓ ĐÃ XỬ LÝ</span>}
                      </div>
                      <p className="text-gray-300 text-xs">{rec.reason}</p>
                    </div>
                    <div className={`w-3 h-3 rounded-full flex-shrink-0 mt-1 ${cfg.dot}`} />
                  </div>

                  {/* Prediction vs Prepped */}
                  <div className="bg-black/30 rounded-xl p-3 mb-3">
                    <div className="flex items-center justify-between text-xs mb-2">
                      <span className="text-gray-400">Đã chuẩn bị</span>
                      <span className="text-gray-400">Dự báo cần</span>
                    </div>
                    <div className="flex items-center gap-3">
                      <span className="text-white font-black text-lg">{rec.prepped}</span>
                      <div className="flex-1 h-3 bg-[#333] rounded-full overflow-hidden">
                        <div
                          className={`h-full rounded-full transition-all ${progress >= 80 ? "bg-green-500" : progress >= 50 ? "bg-yellow-500" : "bg-red-500"}`}
                          style={{ width: `${progress}%` }}
                        />
                      </div>
                      <span className="text-[#FFD23F] font-black text-lg">{rec.predicted}</span>
                    </div>
                    <div className="flex justify-between text-[10px] text-gray-500 mt-1">
                      <span>{rec.unit} đã có</span>
                      <span className={progress < 70 ? "text-red-400 font-bold" : "text-green-400"}>
                        {progress < 100 ? `Thiếu ${rec.predicted - rec.prepped} ${rec.unit}` : "Đủ!"}
                      </span>
                    </div>
                  </div>

                  {/* Action */}
                  {!isAck && (
                    <div className="flex gap-2 items-center">
                      <div className="flex-1 bg-black/30 border border-[#444] rounded-xl px-3 py-2 text-xs font-bold text-[#FFD23F]">
                        👉 {rec.action}
                      </div>
                      <button
                        onClick={() => acknowledgeRec(rec.id)}
                        className="flex items-center gap-1.5 bg-green-600 text-white text-xs font-black px-3 py-2 rounded-xl border border-green-500 whitespace-nowrap hover:bg-green-500 transition-all"
                      >
                        <Check size={12} /> Đã làm
                      </button>
                    </div>
                  )}
                </div>
              </div>
            );
          })}

          {/* Sales prediction chart */}
          <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-white font-black flex items-center gap-2">
                <TrendingUp size={16} className="text-[#FF6B35]" />
                Dự báo doanh số hôm nay
              </h3>
              <div className="flex gap-2">
                {(["today", "week", "month"] as const).map((p) => (
                  <button
                    key={p}
                    onClick={() => setSelectedPeriod(p)}
                    className={`text-xs font-bold px-2.5 py-1 rounded-lg transition-all ${selectedPeriod === p ? "bg-[#FF6B35] text-white" : "text-gray-500 hover:text-white"}`}
                  >
                    {p === "today" ? "Hôm nay" : p === "week" ? "Tuần" : "Tháng"}
                  </button>
                ))}
              </div>
            </div>
            <ResponsiveContainer width="100%" height={200}>
              <AreaChart data={predictionData} margin={{ top: 5, right: 5, left: -20, bottom: 0 }}>
                <defs>
                  <linearGradient id="totalGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#FF6B35" stopOpacity={0.3} />
                    <stop offset="95%" stopColor="#FF6B35" stopOpacity={0} />
                  </linearGradient>
                  <linearGradient id="predGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#8B5CF6" stopOpacity={0.3} />
                    <stop offset="95%" stopColor="#8B5CF6" stopOpacity={0} />
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" stroke="#222" />
                <XAxis dataKey="hour" tick={{ fontSize: 10, fill: "#666" }} />
                <YAxis tick={{ fontSize: 10, fill: "#666" }} />
                <Tooltip
                  contentStyle={{ backgroundColor: "#1A1A1A", border: "1px solid #333", borderRadius: "12px" }}
                  labelStyle={{ color: "#fff", fontWeight: "bold" }}
                  itemStyle={{ color: "#ccc" }}
                />
                <Area type="monotone" dataKey="total" name="Thực tế" stroke="#FF6B35" fill="url(#totalGrad)" strokeWidth={2} />
                <Area type="monotone" dataKey="predictedTotal" name="Dự báo hôm nay" stroke="#8B5CF6" fill="url(#predGrad)" strokeWidth={2} strokeDasharray="5 5" />
              </AreaChart>
            </ResponsiveContainer>
            {weather.deliveryBoost > 0 && (
              <div className="mt-3 flex items-center gap-2 bg-blue-900/30 border border-blue-700/30 rounded-xl p-3 text-xs">
                <CloudRain size={14} className="text-blue-400 flex-shrink-0" />
                <span className="text-blue-300">
                  <span className="font-bold">Hiệu ứng thời tiết:</span> Đường tím là dự báo có tính đến mưa. Đơn Ship tăng <span className="text-green-400 font-bold">+{weather.deliveryBoost}%</span>
                </span>
              </div>
            )}
          </div>

          {/* Best sellers prediction */}
          <div className="bg-[#1A1A1A] border-2 border-[#333] rounded-2xl p-4">
            <h3 className="text-white font-black mb-4 flex items-center gap-2">
              <Package size={16} className="text-[#FFD23F]" />
              Dự báo top món bán chạy hôm nay
            </h3>
            <div className="space-y-3">
              {[
                { name: "Mì trộn đặc biệt", predicted: 85, prev: 70, emoji: "🍜" },
                { name: "Trà sữa trân châu đen", predicted: weather.condition === "sunny" ? 120 : 90, prev: 95, emoji: "🧋" },
                { name: "Phở bò đặc biệt", predicted: 75, prev: 68, emoji: "🍲" },
                { name: "Bánh mì đặc biệt", predicted: weather.condition === "rainy" ? 45 : 60, prev: 55, emoji: "🥖" },
              ].map((item, i) => {
                const isUp = item.predicted >= item.prev;
                return (
                  <div key={i} className="flex items-center gap-3">
                    <span className="text-xl flex-shrink-0">{item.emoji}</span>
                    <div className="flex-1">
                      <div className="flex items-center justify-between mb-1">
                        <span className="text-white text-sm font-bold">{item.name}</span>
                        <div className={`flex items-center gap-1 text-xs font-black ${isUp ? "text-green-400" : "text-red-400"}`}>
                          {isUp ? <TrendingUp size={12} /> : <TrendingDown size={12} />}
                          {item.predicted} suất
                        </div>
                      </div>
                      <div className="h-2 bg-[#333] rounded-full overflow-hidden">
                        <div
                          className="h-full bg-gradient-to-r from-[#FF6B35] to-[#FFD23F] rounded-full"
                          style={{ width: `${Math.min(100, (item.predicted / 150) * 100)}%` }}
                        />
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
