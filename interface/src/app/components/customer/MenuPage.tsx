import { useState, useEffect } from "react";
import { useNavigate, useSearchParams } from "react-router";
import { Star, Plus, SlidersHorizontal, Search, MapPin } from "lucide-react";
import { MENU_ITEMS, formatPrice, type MenuItem } from "../../data/mockData";
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

const SORT_OPTIONS = [
  { id: "popular", label: "Phổ biến nhất" },
  { id: "price_asc", label: "Giá tăng dần" },
  { id: "price_desc", label: "Giá giảm dần" },
  { id: "rating", label: "Đánh giá cao" },
];

export function MenuPage() {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const urlSearch = searchParams.get("search") || "";

  const [category, setCategory] = useState("all");
  const [sort, setSort] = useState("popular");
  const [search, setSearch] = useState(urlSearch);
  const [showFilter, setShowFilter] = useState(false);
  const { addItem, updateQty, cart } = useCartContext();
  const { distance } = useBranch();

  // Sync URL search param
  useEffect(() => {
    if (urlSearch) setSearch(urlSearch);
  }, [urlSearch]);

  let filtered = MENU_ITEMS.filter((item: MenuItem) => {
    if (category !== "all" && item.category !== category) return false;
    if (search && !item.name.toLowerCase().includes(search.toLowerCase()))
      return false;
    return true;
  });

  if (sort === "price_asc")
    filtered = [...filtered].sort((a, b) => a.price - b.price);
  else if (sort === "price_desc")
    filtered = [...filtered].sort((a, b) => b.price - a.price);
  else if (sort === "rating")
    filtered = [...filtered].sort((a, b) => b.rating - a.rating);
  else filtered = [...filtered].sort((a, b) => b.sold - a.sold);

  const getQty = (menuItemId: string) => {
    return cart
      .filter((c) => c.menuItemId === menuItemId)
      .reduce((sum, c) => sum + c.quantity, 0);
  };

  const handleAdd = (item: MenuItem) => {
    addItem({
      menuItemId: item.id,
      name: item.name,
      image: item.image,
      price: item.price,
    });
    toast.success(`Đã thêm ${item.name}`, { icon: "🛒" });
  };

  const handleRemove = (item: MenuItem) => {
    const cartItem = cart.find((c) => c.menuItemId === item.id);
    if (cartItem) updateQty(cartItem.id, -1);
  };

  const totalItems = cart.reduce((sum, c) => sum + c.quantity, 0);
  const subtotal = cart.reduce((sum, c) => sum + c.price * c.quantity, 0);

  return (
    <div className="pb-4">
      {/* Search */}
      <div className="px-4 pt-4 mb-3">
        <div className="relative">
          <Search
            size={16}
            className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
          />
          <input
            value={search}
            onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
              setSearch(e.target.value)
            }
            placeholder="Tìm trong thực đơn..."
            className="w-full pl-9 pr-4 py-2.5 border-2 border-[#1C1C1C] rounded-xl bg-white shadow-[2px_2px_0px_#1C1C1C] text-sm outline-none focus:border-[#FF6B35] transition-all"
          />
          <button
            onClick={() => setShowFilter(!showFilter)}
            className="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg bg-[#FFD23F] border border-[#1C1C1C]"
          >
            <SlidersHorizontal size={14} />
          </button>
        </div>
      </div>

      {/* Filter panel */}
      {showFilter && (
        <div className="mx-4 mb-3 bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] p-4">
          <p className="text-xs font-black text-[#1C1C1C] mb-2 uppercase">
            Sắp xếp theo
          </p>
          <div className="flex flex-wrap gap-2">
            {SORT_OPTIONS.map((opt) => (
              <button
                key={opt.id}
                onClick={() => setSort(opt.id)}
                className={`text-xs font-bold px-3 py-1.5 rounded-xl border-2 border-[#1C1C1C] transition-all ${
                  sort === opt.id
                    ? "bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]"
                    : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"
                }`}
              >
                {opt.label}
              </button>
            ))}
          </div>
        </div>
      )}

      {/* Categories - Horizontal sticky */}
      <div className="sticky top-0 bg-[#FAFAF8] z-10 px-4 pb-3">
        <div className="flex gap-2 overflow-x-auto scrollbar-hide">
          {CATEGORIES.map((cat) => (
            <button
              key={cat.id}
              onClick={() => setCategory(cat.id)}
              className={`flex-shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-[#1C1C1C] text-xs font-bold transition-all ${
                category === cat.id
                  ? "bg-[#FF6B35] text-white shadow-[2px_2px_0px_#1C1C1C]"
                  : "bg-white text-[#1C1C1C] shadow-[2px_2px_0px_#1C1C1C]"
              }`}
            >
              {cat.emoji} {cat.label}
            </button>
          ))}
        </div>
      </div>

      {/* Results count */}
      <div className="px-4 mb-3">
        <span className="text-xs text-gray-500">
          {filtered.length} món ·{" "}
          {category !== "all"
            ? CATEGORIES.find((c) => c.id === category)?.label
            : "Tất cả danh mục"}
          {search && ` · Tìm "${search}"`}
        </span>
      </div>

      {/* Empty state */}
      {filtered.length === 0 && (
        <div className="text-center py-12 px-4">
          <div className="text-6xl mb-3">🍜</div>
          <p className="font-black text-[#1C1C1C] text-lg">
            Không tìm thấy món nào
          </p>
          <p className="text-gray-500 text-sm mt-1">
            Thử tìm kiếm từ khoá khác nhé!
          </p>
        </div>
      )}

      {/* Menu list */}
      <div className="px-4 space-y-3">
        {filtered.map((item) => {
          const qty = getQty(item.id);
          return (
            <div
              key={item.id}
              onClick={() => navigate(`/product/${item.id}`)}
              className="bg-white border-2 border-[#1C1C1C] rounded-2xl shadow-[4px_4px_0px_#1C1C1C] overflow-hidden flex cursor-pointer hover:shadow-[2px_2px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all"
            >
              <div className="relative w-28 h-28 flex-shrink-0 overflow-hidden">
                <img
                  src={item.image}
                  alt={item.name}
                  className="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                />
                {item.isNew && (
                  <div className="absolute top-1 left-1 bg-[#FFD23F] border border-[#1C1C1C] text-[#1C1C1C] text-[9px] font-black px-1.5 py-0.5 rounded-full">
                    NEW
                  </div>
                )}
                {item.isBestSeller && (
                  <div className="absolute top-1 right-1 bg-[#FF6B35] text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">
                    🔥
                  </div>
                )}
              </div>
              <div className="flex-1 p-3 flex flex-col justify-between">
                <div>
                  <div className="font-black text-[#1C1C1C] text-sm leading-tight">
                    {item.name}
                  </div>
                  <div className="text-gray-500 text-[10px] mt-0.5 line-clamp-1">
                    {item.description}
                  </div>
                  <div className="flex items-center gap-2 mt-1">
                    <div className="flex items-center gap-0.5">
                      <Star
                        size={10}
                        className="fill-[#FFD23F] text-[#FFD23F]"
                      />
                      <span className="text-[10px] text-gray-600">
                        {item.rating}
                      </span>
                    </div>
                    <span className="text-[10px] text-gray-400">·</span>
                    <span className="text-[10px] text-gray-400">
                      Đã bán {item.sold}+
                    </span>
                    <span className="text-[10px] text-gray-400">·</span>
                    <span className="text-[10px] text-gray-400 flex items-center gap-0.5">
                      <MapPin size={8} />
                      {distance.toFixed(1)}km
                    </span>
                  </div>
                </div>
                <div className="flex items-center justify-between mt-2">
                  <span className="font-black text-[#FF6B35]">
                    {formatPrice(item.price)}
                  </span>
                  <div
                    className="flex items-center gap-1.5"
                    onClick={(e: React.MouseEvent<HTMLDivElement>) =>
                      e.stopPropagation()
                    }
                  >
                    {qty > 0 && (
                      <>
                        <button
                          onClick={() => handleRemove(item)}
                          className="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-white flex items-center justify-center shadow-[1px_1px_0px_#1C1C1C] text-[#1C1C1C] font-black text-lg"
                        >
                          −
                        </button>
                        <span className="font-black text-[#1C1C1C] text-sm min-w-[16px] text-center">
                          {qty}
                        </span>
                      </>
                    )}
                    <button
                      onClick={() => handleAdd(item)}
                      className="w-7 h-7 rounded-lg border-2 border-[#1C1C1C] bg-[#FF6B35] text-white flex items-center justify-center shadow-[2px_2px_0px_#1C1C1C] hover:shadow-[1px_1px_0px_#1C1C1C] transition-all"
                    >
                      <Plus size={14} />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Floating cart summary */}
      {totalItems > 0 && (
        <div className="fixed bottom-20 left-4 right-4 max-w-[398px] mx-auto z-20">
          <button
            onClick={() => navigate("/cart")}
            className="w-full bg-[#FF6B35] text-white font-black py-4 rounded-2xl border-2 border-[#1C1C1C] shadow-[4px_4px_0px_#1C1C1C] flex items-center justify-between px-5 hover:shadow-[2px_2px_0px_#1C1C1C] hover:translate-x-[1px] hover:translate-y-[1px] transition-all"
          >
            <span className="bg-white/20 px-2 py-0.5 rounded-lg text-sm">
              {totalItems} món
            </span>
            <span>Xem giỏ hàng →</span>
            <span>{formatPrice(subtotal)}</span>
          </button>
        </div>
      )}
    </div>
  );
}

