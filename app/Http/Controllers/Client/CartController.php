<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AddToCartRequest;
use App\Http\Requests\Client\UpdateCartQtyRequest;
use App\Http\Requests\Client\ApplyVoucherRequest;
use App\Http\Requests\Client\PlaceOrderRequest;
use App\Models\Catalog\Product;
use App\Models\Catalog\ProductOptionValue;
use App\Models\Promotion\Voucher;
use App\Models\Promotion\VoucherUsage;
use App\Models\System\Branch;
use App\Models\User\CartItem;
use App\Models\User\CartVoucher;
use Illuminate\Support\Facades\DB;
use App\Models\Order\Order;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    // Kiểm tra đăng nhập
    private function isLoggedIn(): bool
    {
        return auth()->check();
    }

    // Lấy user_id hoặc session_key
    private function getUserId()
    {
        return $this->isLoggedIn() ? auth()->id() : null;
    }

    private function getSessionKey()
    {
        return !$this->isLoggedIn() ? session()->getId() : null;
    }

    // Lấy cart từ database
    private function getCart(): array
    {
        $userId = $this->getUserId();
        $sessionKey = $this->getSessionKey();

        $items = CartItem::where(function ($query) use ($userId, $sessionKey) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_key', $sessionKey);
            }
        })->with('product:id,name,image,base_price')->get();

        $cart = [];
        foreach ($items as $item) {
            $key = $item->product_id . '_' . md5(json_encode($item->options ?? []));
            $cart[$key] = [
                'id'            => $key,
                'product_id'    => $item->product_id,
                'name'          => $item->product?->name,
                'image'         => $item->product?->image,
                'price'         => $item->price,
                'quantity'      => $item->quantity,
                'options'       => $item->options ?? [],
                'option_labels' => $item->option_labels ?? [],
                'note'          => $item->note,
            ];
        }
        return $cart;
    }

    // Lấy voucher đã áp dụng
    private function getAppliedVoucher()
    {
        $userId = $this->getUserId();
        $sessionKey = $this->getSessionKey();

        $cartVoucher = CartVoucher::where(function ($query) use ($userId, $sessionKey) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_key', $sessionKey);
            }
        })->with('voucher')->first();

        if (!$cartVoucher || !$cartVoucher->voucher) {
            return null;
        }

        $voucher = $cartVoucher->voucher;
        return [
            'id'       => $voucher->id,
            'code'     => $voucher->code,
            'discount' => $voucher->value,
            'type'    => $voucher->type,
        ];
    }

    public function index()
    {
        $cart = $this->getCart();
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        $branches = Branch::where('is_active', true)->get();
        $vouchers = Voucher::where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereRaw('max_uses IS NULL OR used_count < max_uses')
            ->get();

        $appliedVoucher = $this->getAppliedVoucher();
        $shippingFee = 15000;
        if ($appliedVoucher && $appliedVoucher['type'] === 'shipping') {
            $shippingFee = max(0, $shippingFee - $appliedVoucher['discount']);
        }

        return view('client.cart.index', compact('cart', 'subtotal', 'branches', 'vouchers', 'appliedVoucher', 'shippingFee'));
    }

    public function cartSummary()
    {
        return $this->index();
    }

    public function add(AddToCartRequest $request)
    {
        $product = Product::with('options.values')->findOrFail($request->product_id);
        $selectedOptions = $request->options ?? [];

        // Nếu sản phẩm có biến thể nhưng người dùng không chọn, tự động chọn biến thể giá thấp nhất
        if ($product->options()->exists() && empty($selectedOptions)) {
            $defaultOptions = $product->options()
                ->with('values')
                ->get()
                ->flatMap(fn($opt) => $opt->values)
                ->sortBy('extra_price')
                ->first();

            if ($defaultOptions) {
                $selectedOptions = [$defaultOptions->id];
            } else {
                throw ValidationException::withMessages([
                    'options' => 'Vui lòng chọn biến thể cho sản phẩm này.',
                ]);
            }
        }

        $extraPrice = 0;
        $optionLabels = [];
        if (!empty($selectedOptions)) {
            $values = ProductOptionValue::whereIn('id', $selectedOptions)->get();
            $extraPrice = $values->sum('extra_price');
            $optionLabels = $values->pluck('label')->toArray();
        }

        $optionsJson = json_encode($selectedOptions);
        $userId = $this->getUserId();
        $sessionKey = $this->getSessionKey();

        // Tìm item đã tồn tại
        $existing = CartItem::where('product_id', $product->id)
            ->where('options', $optionsJson)
            ->where(function ($query) use ($userId, $sessionKey) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_key', $sessionKey);
                }
            })
            ->first();

        if ($existing) {
            $existing->increment('quantity', $request->quantity);
            $existing->refresh();
        } else {
            CartItem::create([
                'user_id'       => $userId,
                'product_id'    => $product->id,
                'options'       => $selectedOptions,
                'option_labels' => $optionLabels,
                'quantity'      => $request->quantity,
                'price'        => $product->base_price + $extraPrice,
                'note'          => $request->note,
                'session_key'  => $sessionKey,
            ]);
        }

        $count = CartItem::where(function ($query) use ($userId, $sessionKey) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_key', $sessionKey);
            }
        })->sum('quantity');

        return response()->json(['ok' => true, 'count' => $count]);
    }

    public function remove(string $id)
    {
        $cart = $this->getCart();
        if (!isset($cart[$id])) {
            return back();
        }

        $productId = $cart[$id]['product_id'];
        $options = $cart[$id]['options'] ?? [];
        $optionsJson = json_encode($options);
        $userId = $this->getUserId();
        $sessionKey = $this->getSessionKey();

        CartItem::where('product_id', $productId)
            ->where('options', $optionsJson)
            ->where(function ($query) use ($userId, $sessionKey) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_key', $sessionKey);
                }
            })
            ->delete();

        return back();
    }

    public function updateQty(UpdateCartQtyRequest $request, string $id)
    {
        $cart = $this->getCart();
        if (!isset($cart[$id])) {
            return response()->json(['ok' => false, 'message' => 'Item not found']);
        }

        $productId = $cart[$id]['product_id'];
        // Get CURRENT/OLD options from stored cart (what was originally saved)
        $currentOptions = $cart[$id]['options'] ?? [];
        $currentOptionsJson = json_encode($currentOptions);

        $newOptions = $request->options ?? null;
        $quantity = $request->quantity ?? 1;
        $userId = $this->getUserId();
        $sessionKey = $this->getSessionKey();

        // Find the EXISTING item using the OLD options - key insight!
        $item = CartItem::where('product_id', $productId)
            ->where('options', $currentOptionsJson)
            ->where(function ($query) use ($userId, $sessionKey) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_key', $sessionKey);
                }
            })
            ->first();

        if (!$item) {
            return response()->json(['ok' => false, 'message' => 'Item not found in DB']);
        }

        if ($quantity <= 0) {
            $item->delete();
            return response()->json(['ok' => true]);
        }

        // Recalculate price if options changed
        $optionsToSave = $newOptions ?? $currentOptions;
        $price = $item->price;
        $optionLabels = $cart[$id]['option_labels'] ?? [];

        if ($newOptions !== null && $newOptions !== $currentOptions) {
            $values = ProductOptionValue::whereIn('id', $newOptions)->get();
            $extraPrice = $values->sum('extra_price');
            $optionLabels = $values->pluck('label')->toArray();
            $product = Product::find($productId);
            $price = $product->base_price + $extraPrice;
        }

        $item->update([
            'quantity'     => $quantity,
            'options'      => $optionsToSave,
            'option_labels' => $optionLabels,
            'price'       => $price,
        ]);

        return response()->json(['ok' => true]);
    }

    public function applyVoucher(ApplyVoucherRequest $request)
    {
        $cart = $this->getCart();
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        $voucher = Voucher::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->first();

        if (!$voucher || !$voucher->isValid()) {
            return response()->json(['ok' => false, 'message' => 'Voucher không hợp lệ hoặc đã hết hạn.']);
        }

        if ($subtotal < $voucher->min_order) {
            return response()->json(['ok' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher->min_order) . 'đ.']);
        }

        $discount = match ($voucher->type) {
            'flat' => $voucher->value,
            'percent' => min($subtotal * $voucher->value / 100, $voucher->max_discount ?? PHP_INT_MAX),
            'shipping' => 15000,
            default => 0,
        };

        $userId = $this->getUserId();
        $sessionKey = $this->getSessionKey();

        // Xóa voucher cũ
        CartVoucher::where(function ($query) use ($userId, $sessionKey) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_key', $sessionKey);
            }
        })->delete();

        // Thêm voucher mới
        CartVoucher::create([
            'user_id'      => $userId,
            'voucher_id'  => $voucher->id,
            'session_key' => $sessionKey,
        ]);

        return response()->json(['ok' => true, 'code' => $voucher->code, 'discount' => $discount, 'type' => $voucher->type]);
    }

    public function checkout()
    {
        return redirect()->route('client.cart')->with('error', 'Sử dụng checkout trực tiếp từ giỏ hàng.');
    }

    public function placeOrder(PlaceOrderRequest $request)
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Giỏ hàng trống.']);
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $shippingFee = $request->delivery_mode === 'delivery' ? 15000 : 0;
        $appliedVoucher = $this->getAppliedVoucher();
        $discountAmount = 0;
        $voucherId = null;

        if ($appliedVoucher) {
            $discountAmount = $appliedVoucher['type'] === 'shipping'
                ? min($appliedVoucher['discount'], $shippingFee)
                : $appliedVoucher['discount'];
            $voucherId = $appliedVoucher['id'];
        }

        $grandTotal = max(0, $subtotal + $shippingFee - $discountAmount);
        $order = null;

        $userId = $this->getUserId();
        $sessionKey = $this->getSessionKey();

        DB::transaction(function () use ($request, $cart, $subtotal, $shippingFee, $discountAmount, $grandTotal, $voucherId, $appliedVoucher, &$order) {
            $branchCode = Branch::find($request->branch_id)->name;
            $branchCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $branchCode), 0, 2));

            $order = Order::create([
                'order_number'    => Order::generateOrderNumber($branchCode),
                'user_id'         => auth()->id(),
                'branch_id'       => $request->branch_id,
                'voucher_id'      => $voucherId,
                'status'          => 'pending',
                'delivery_mode'   => $request->delivery_mode,
                'payment_method'  => $request->payment_method,
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_fee'    => $shippingFee,
                'grand_total'     => $grandTotal,
                'delivery_address' => $request->delivery_address,
            ]);

            foreach ($cart as $item) {
                $orderItem = $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'note'       => $item['note'],
                ]);

                if (!empty($item['options'] ?? [])) {
                    foreach ($item['options'] as $optionValueId) {
                        $orderItem->options()->create([
                            'option_value_id' => $optionValueId,
                        ]);
                    }
                }
            }

            if ($voucherId) {
                Voucher::where('id', $voucherId)->increment('used_count');
                VoucherUsage::create([
                    'voucher_id'      => $voucherId,
                    'user_id'       => auth()->id(),
                    'order_id'      => $order->id,
                    'discount_applied' => $appliedVoucher['discount'],
                ]);
            }
        });

        // Xóa cart và voucher sau khi đặt hàng
        CartItem::where(function ($query) use ($userId, $sessionKey) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_key', $sessionKey);
            }
        })->delete();

        CartVoucher::where(function ($query) use ($userId, $sessionKey) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_key', $sessionKey);
            }
        })->delete();

        return redirect()->route('client.order.show', $order->id)->with('success', 'Đặt hàng thành công! 🎉');
    }
}
