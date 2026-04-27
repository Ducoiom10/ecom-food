import { useState, useCallback, useEffect } from "react";

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

export function useCart() {
  const [cart, setCart] = useState<CartItem[]>(loadCart);

  useEffect(() => {
    saveCart(cart);
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

  const totalItems = cart.reduce((sum: number, item: CartItem) => sum + item.quantity, 0);
  const subtotal = cart.reduce(
    (sum: number, item: CartItem) => sum + item.price * item.quantity,
    0
  );

  return {
    cart,
    addItem,
    updateQty,
    removeItem,
    clearCart,
    totalItems,
    subtotal,
  };
}

