import {
  createContext,
  useContext,
  useState,
  useCallback,
  useEffect,
} from "react";

export interface CartItem {
  id: string;
  menuItemId: string;
  name: string;
  image: string;
  price: number;
  quantity: number;
  size?: string;
  toppings?: string[];
  note?: string;
}

interface CartContextType {
  cart: CartItem[];
  addItem: (item: Omit<CartItem, "id" | "quantity"> & { quantity?: number }) => void;
  updateQty: (id: string, delta: number) => void;
  removeItem: (id: string) => void;
  clearCart: () => void;
  totalItems: number;
  subtotal: number;
}

const CartContext = createContext<CartContextType | undefined>(undefined);

const CART_KEY = "ba_anh_em_cart";

function loadCart(): CartItem[] {
  try {
    const raw = localStorage.getItem(CART_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

function saveCart(cart: CartItem[]) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

export function CartProvider({ children }: { children: React.ReactNode }) {
  const [cart, setCart] = useState<CartItem[]>(loadCart);

  useEffect(() => {
    saveCart(cart);
    // Dispatch custom event để các tab/page khác biết cart đã thay đổi
    window.dispatchEvent(new Event("cart-updated"));
  }, [cart]);

  const addItem = useCallback(
    (item: Omit<CartItem, "id" | "quantity"> & { quantity?: number }) => {
      setCart((prev: CartItem[]) => {
        const existIdx = prev.findIndex(
          (c: CartItem) =>
            c.menuItemId === item.menuItemId &&
            c.size === (item.size || "M") &&
            JSON.stringify(c.toppings || []) ===
              JSON.stringify(item.toppings || [])
        );
        if (existIdx >= 0) {
          const next = [...prev];
          next[existIdx] = {
            ...next[existIdx],
            quantity: next[existIdx].quantity + (item.quantity || 1),
          };
          return next;
        }
        return [
          ...prev,
          {
            ...item,
            id: `${item.menuItemId}-${Date.now()}`,
            quantity: item.quantity || 1,
            size: item.size || "M",
            toppings: item.toppings || [],
          },
        ];
      });
    },
    []
  );

  const updateQty = useCallback((id: string, delta: number) => {
    setCart((prev: CartItem[]) =>
      prev
        .map((item: CartItem) =>
          item.id === id
            ? { ...item, quantity: Math.max(0, item.quantity + delta) }
            : item
        )
        .filter((item: CartItem) => item.quantity > 0)
    );
  }, []);

  const removeItem = useCallback((id: string) => {
    setCart((prev: CartItem[]) => prev.filter((item: CartItem) => item.id !== id));
  }, []);

  const clearCart = useCallback(() => {
    setCart([]);
  }, []);

  const totalItems = cart.reduce(
    (sum: number, item: CartItem) => sum + item.quantity,
    0
  );
  const subtotal = cart.reduce(
    (sum: number, item: CartItem) => sum + item.price * item.quantity,
    0
  );

  return (
    <CartContext.Provider
      value={{ cart, addItem, updateQty, removeItem, clearCart, totalItems, subtotal }}
    >
      {children}
    </CartContext.Provider>
  );
}

export function useCartContext(): CartContextType {
  const ctx = useContext(CartContext);
  if (!ctx) throw new Error("useCartContext must be used within CartProvider");
  return ctx;
}

