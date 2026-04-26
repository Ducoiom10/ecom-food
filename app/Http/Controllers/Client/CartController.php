<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart     = session('cart', []);
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $vouchers = Voucher::where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereRaw('max_uses IS NULL OR used_count < max_uses')
            ->get();

        $cartProductIds = collect($cart)->pluck('product_id')->filter()->values();
        $upsell = Product::whereNotIn('id', $cartProductIds)
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();

        return view('client.cart', compact('cart', 'subtotal', 'vouchers', 'upsell'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:20',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart    = session('cart', []);

        $toppings   = $request->toppings ?? [];
        $key        = $product->id . '_' . ($request->size ?? '') . '_' . implode(',', $toppings);
        $extraPrice = (int) $request->extra_price;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'id'         => $key,
                'product_id' => $product->id,
                'name'       => $product->name,
                'image'      => $product->image,
                'price'      => $product->base_price + $extraPrice,
                'quantity'   => (int) $request->quantity,
                'size'       => $request->size,
                'toppings'   => $toppings,
                'note'       => $request->note,
            ];
        }

        session(['cart' => $cart]);

        return response()->json(['ok' => true, 'count' => count($cart)]);
    }

    public function remove(string $id)
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);

        return back();
    }

    public function applyVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $cart     = session('cart', []);
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

        $discount = match($voucher->type) {
            'flat'     => $voucher->value,
            'percent'  => min($subtotal * $voucher->value / 100, $voucher->max_discount ?? PHP_INT_MAX),
            'shipping' => 15000,
            default    => 0,
        };

        session(['applied_voucher' => ['id' => $voucher->id, 'code' => $voucher->code, 'discount' => $discount, 'type' => $voucher->type]]);

        return response()->json(['ok' => true, 'code' => $voucher->code, 'discount' => $discount, 'type' => $voucher->type]);
    }

    public function checkout()
    {
        return $this->index();
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method'   => 'required|in:momo,bank,cod,zalopay',
            'delivery_mode'    => 'required|in:pickup,delivery',
            'branch_id'        => 'required|exists:branches,id',
            'delivery_address' => 'required_if:delivery_mode,delivery|nullable|string',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Giỏ hàng trống.']);
        }

        $subtotal       = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $shippingFee    = $request->delivery_mode === 'delivery' ? 15000 : 0;
        $appliedVoucher = session('applied_voucher');
        $discountAmount = 0;
        $voucherId      = null;

        if ($appliedVoucher) {
            $discountAmount = $appliedVoucher['type'] === 'shipping'
                ? min($appliedVoucher['discount'], $shippingFee)
                : $appliedVoucher['discount'];
            $voucherId = $appliedVoucher['id'];
        }

        $grandTotal = max(0, $subtotal + $shippingFee - $discountAmount);

        \DB::transaction(function () use ($request, $cart, $subtotal, $shippingFee, $discountAmount, $grandTotal, $voucherId, $appliedVoucher) {
            // Tạo order
            $branchCode = \App\Models\Branch::find($request->branch_id)->name;
            $branchCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $branchCode), 0, 2));

            $order = \App\Models\Order::create([
                'order_number'     => \App\Models\Order::generateOrderNumber($branchCode),
                'user_id'          => auth()->id(),
                'branch_id'        => $request->branch_id,
                'voucher_id'       => $voucherId,
                'status'           => 'pending',
                'delivery_mode'    => $request->delivery_mode,
                'payment_method'   => $request->payment_method,
                'subtotal'         => $subtotal,
                'discount_amount'  => $discountAmount,
                'shipping_fee'     => $shippingFee,
                'grand_total'      => $grandTotal,
                'delivery_address' => $request->delivery_address,
            ]);

            // Tạo order items
            foreach ($cart as $item) {
                $orderItem = $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'note'       => $item['note'],
                ]);

                // TODO: lưu option_value_ids khi có
            }

            // Cập nhật voucher used_count
            if ($voucherId) {
                \App\Models\Voucher::where('id', $voucherId)->increment('used_count');
                \App\Models\VoucherUsage::create([
                    'voucher_id'       => $voucherId,
                    'user_id'          => auth()->id(),
                    'order_id'         => $order->id,
                    'discount_applied' => $appliedVoucher['discount'],
                ]);
            }
        });

        session()->forget(['cart', 'applied_voucher']);

        return redirect()->route('client.profile')->with('success', 'Đặt hàng thành công! 🎉');
    }
}
