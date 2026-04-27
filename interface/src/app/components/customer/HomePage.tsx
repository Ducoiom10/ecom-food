import { useState } from "react";
import { useNavigate } from "react-router";
import {
  Star,
  Plus,
  Zap,
  Flame,
  Users,
  ArrowRight,
  ChevronRight,
  TrendingUp,
  Clock,
  MapPin,
} from "lucide-react";
import { MENU_ITEMS, COMBOS, REVIEWS, formatPrice } from "../../data/mockData";
import { useCartContext } from "../../context/CartContext";
import { useBranch } from "../../hooks/useBranch";
import { toast } from "sonner";

const CATEGORIES = [
  { id: "all", label: "Tất cả", emoji: "🍽️" },
  { id: "noodles", label: "Mì & Phở", emoji: "🍜" },
  { id: "rice", label: "Cơm", emoji: "🍚" },
  { id: "snacks", label: "Ăn vặt", emoji: "🍗" },
  { id: "drinks", label: "Đồ uống", emoji: "🧋" },
];

export function HomePage() {
  const navigate = useNavigate();
  const [activeCategory, setActiveCategory] = useState("all");
  const { addItem, totalItems } = useCartContext();
  const { branch, distance } = useBranch();

  const filteredItems = MENU_ITEMS.filter((item) => {
    if (activeCategory !== "all" && item.category !== activeCategory)
      return false;
    return true;
  });

  const handleAddToCart = (item: (typeof MENU_ITEMS)[0]) => {
    addItem({
      menuItemId: item.id,
      name: item.name,
      image: item.image,
      price: item.price,
    });
    toast.success(`Đã thêm ${item.name}`, {
      description: `Giỏ hàng hiện có ${totalItems + 1} món`,
      icon: "🛒",
    });
  };

  return (
    <div className="pb-4">
      {/* Hero Banner */}
      <div className="mx-4 mt-4 relative overflow-hidden bg-[#1C1C1C] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#FF6B35] p-5">
        <div className="relative z-10">
          <div className="flex items-center gap-2 mb-2">
            <span className="bg-[#FFD23F] text-[#1C1C1C] text-xs font-black px-2 py-0.5 rounded-full border border-[#1C1C1C]">
              🔥 HOT DEAL
            </span>
            <span className="text-white/60 text-xs">Hôm nay thôi!</span>
          </div>
          <h2 className="text-white text-xl font-black mb-1">
            Combo Trưa
            <br />
            Văn Phòng 🏢
          </h2>
          <p className="text-white/70 text-xs mb-4">
            Mì trộn + Trà sữa chỉ còn{" "}
            <span className="text-[#FFD23F] font-black">65.000đ</span>
          </p>
          <button
            onClick={() => navigate("/menu")}
            className="bg-[#FF6B35] text-white text-sm font-black px-4 py-2 rounded-xl border-2 border-[#FF6B35] shadow-[2px_2px_0px_white] hover:shadow-[1px_1px_0px_white] hover:translate-x-[1px] hover:translate-y-[1px] transition-all flex items-center gap-1.5"
          >
            Đặt ngay <ArrowRight size={14} />
          </button>
        </div>
        <div className="absolute right-0 top-0 bottom-0 w-32 flex items-center justify-center opacity-20 text-6xl">
          🍜
        </div>
      </div>

      {/* Group Order CTA */}
      <div className="mx-4 mt-3">
        <button
          onClick={() => navigate("/group-order")}
          className="w-full bg-[#FFD23F] border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4 flex items-center gap-3 hover:shadow-[2px_2px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all"
        >
          <div className="w-12 h-12 bg-[#1C1C1C] rounded-xl flex items-center justify-center flex-shrink-0">
            <Users size={24} className="text-[#FFD23F]" />
          </div>
          <div className="text-left flex-1">
            <div className="font-black text-[#1C1C1C] flex items-center gap-2">
              Đặt đơn nhóm
              <span className="bg-[#FF6B35] text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">
                NEW
              </span>
            </div>
            <p className="text-xs text-[#1C1C1C]/70 mt-0.5">
              Tạo phòng, gửi link, mỗi ngườitự chọn — chia bill tự động!
            </p>
          </div>
          <ChevronRight size={20} className="text-[#1C1C1C] flex-shrink-0" />
        </button>
      </div>

      {/* Branch info */}
      <div className="px-4 mt-3">
        <div className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3 flex items-center gap-2">
          <MapPin size={14} className="text-[#FF6B35]" />
          <span className="text-xs font-bold text-[#1C1C1C]">
            {branch.name}
          </span>
          <span className="text-[10px] text-gray-400">·</span>
          <span className="text-[10px] text-gray-500">
            Cách bạn {distance.toFixed(1)}km
          </span>
          <span className="text-[10px] text-gray-400">·</span>
          <span className="text-[10px] text-green-600 font-bold">
            Đang mở cửa
          </span>
        </div>
      </div>

      {/* Categories */}
      <div className="px-4 mt-4">
        <div className="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
          {CATEGORIES.map((cat) => (
            <button
              key={cat.id}
              onClick={() => setActiveCategory(cat.id)}
              className={`flex-shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs font-bold transition-all ${
                activeCategory === cat.id
                  ? "bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]"
                  : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C]"
              }`}
            >
              {cat.emoji} {cat.label}
            </button>
          ))}
        </div>
      </div>

      {/* Combos Section */}
      <div className="mt-5">
        <div className="px-4 flex items-center justify-between mb-3">
          <h3 className="font-black text-[#1C1C1C] flex items-center gap-2">
            <TrendingUp size={16} className="text-[#FF6B35]" /> Combo tiết kiệm
          </h3>
          <button
            onClick={() => navigate("/menu")}
            className="text-[#FF6B35] text-xs font-bold flex items-center gap-1"
          >
            Xem thêm <ChevronRight size={14} />
          </button>
        </div>
        <div className="flex gap-3 px-4 overflow-x-auto pb-2 scrollbar-hide">
          {COMBOS.map((combo) => (
            <div
              key={combo.id}
              className="flex-shrink-0 w-52 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden cursor-pointer hover:shadow-[2px_2px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all"
              onClick={() => navigate("/menu")}
            >
              <div className="relative h-28 overflow-hidden">
                <img
                  src={combo.image}
                  alt={combo.name}
                  className="w-full h-full object-cover"
                />
                <div className="absolute top-2 left-2 bg-[#FF6B35] text-white text-[10px] font-black px-2 py-0.5 rounded-full border border-white shadow">
                  Tiết kiệm {formatPrice(combo.savings)}
                </div>
              </div>
              <div className="p-3">
                <div className="font-black text-[#1C1C1C] text-sm">
                  {combo.name}
                </div>
                <div className="text-xs text-gray-500 mt-0.5">
                  {combo.description}
                </div>
                <div className="flex items-center justify-between mt-2">
                  <div>
                    <span className="font-black text-[#FF6B35]">
                      {formatPrice(combo.comboPrice)}
                    </span>
                    <span className="text-xs text-gray-400 line-through ml-1">
                      {formatPrice(combo.originalPrice)}
                    </span>
                  </div>
                  <button className="bg-[#1C1C1C] text-white w-7 h-7 rounded-lg flex items-center justify-center">
                    <Plus size={14} />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Best Sellers */}
      <div className="mt-5 px-4">
        <h3 className="font-black text-[#1C1C1C] mb-3 flex items-center gap-2">
          <Flame size={16} className="text-[#FF6B35]" /> Bán chạy nhất
        </h3>
        <div className="grid grid-cols-2 gap-3">
          {filteredItems.map((item) => (
            <div
              key={item.id}
              className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden cursor-pointer hover:shadow-[2px_2px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all"
              onClick={() => navigate(`/product/${item.id}`)}
            >
              <div className="relative h-32 overflow-hidden">
                <img
                  src={item.image}
                  alt={item.name}
                  className="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                />
                {item.isNew && (
                  <div className="absolute top-2 left-2 bg-[#FFD23F] border border-[#1C1C1C] text-[#1C1C1C] text-[9px] font-black px-1.5 py-0.5 rounded-full">
                    ✨ NEW
                  </div>
                )}
                {item.isBestSeller && (
                  <div className="absolute top-2 right-2 bg-[#FF6B35] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">
                    🔥 TOP
                  </div>
                )}
              </div>
              <div className="p-2.5">
                <div className="font-black text-[#1C1C1C] text-xs leading-tight line-clamp-2">
                  {item.name}
                </div>
                <div className="flex items-center gap-1 mt-1">
                  <Star
                    size={10}
                    className="fill-[#FFD23F] text-[#FFD23F]"
                  />
                  <span className="text-[10px] text-gray-600">
                    {item.rating}
                  </span>
                  <span className="text-[10px] text-gray-400">
                    · Đã bán {item.sold}+
                  </span>
                </div>
                <div className="flex items-center gap-1 mt-0.5">
                  <MapPin size={10} className="text-gray-400" />
                  <span className="text-[10px] text-gray-400">
                    {distance.toFixed(1)}km
                  </span>
                </div>
                <div className="flex items-center justify-between mt-2">
                  <span className="font-black text-[#FF6B35] text-sm">
                    {formatPrice(item.price)}
                  </span>
                  <button
                    onClick={(e: React.MouseEvent<HTMLButtonElement>) => {
                      e.stopPropagation();
                      handleAddToCart(item);
                    }}
                    className="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] flex items-center justify-center transition-all bg-[#FFD23F] text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C]"
                  >
                    <Plus size={12} />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Reviews */}
      <div className="mt-5 px-4">
        <h3 className="font-black text-[#1C1C1C] mb-3 flex items-center gap-2">
          ⭐ Khách hàng nói gì?
        </h3>
        <div className="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
          {REVIEWS.map((review) => (
            <div
              key={review.id}
              className="flex-shrink-0 w-64 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[3px_3px_0px_#1C1C1C] p-3"
            >
              <div className="flex items-center gap-2 mb-2">
                <div className="w-8 h-8 bg-[#FF6B35] rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0">
                  {review.avatar}
                </div>
                <div>
                  <div className="font-bold text-xs text-[#1C1C1C]">
                    {review.user}
                  </div>
                  <div className="flex gap-0.5">
                    {Array.from({ length: review.rating }).map((_, i) => (
                      <Star
                        key={i}
                        size={10}
                        className="fill-[#FFD23F] text-[#FFD23F]"
                      />
                    ))}
                  </div>
                </div>
              </div>
              <p className="text-xs text-gray-600 leading-relaxed mb-2">
                "{review.comment}"
              </p>
              <div className="flex items-center gap-2">
                <img
                  src={review.img}
                  alt=""
                  className="w-8 h-8 rounded-lg object-cover border border-gray-200"
                />
                <div>
                  <div className="text-[10px] text-gray-500">{review.item}</div>
                  <div className="text-[10px] text-gray-400">{review.time}</div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

