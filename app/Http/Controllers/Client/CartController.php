<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart     = session('cart', []);
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        return view('client.cart', compact('cart', 'subtotal'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart    = session('cart', []);
        $key     = $request->product_id . '_' . ($request->size ?? '') . '_' . implode(',', $request->toppings ?? []);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'id'         => $key,
                'product_id' => $product->id,
                'name'       => $product->name,
                'image'      => $product->image,
                'price'      => $product->base_price + ($request->extra_price ?? 0),
                'quantity'   => $request->quantity,
                'size'       => $request->size,
                'toppings'   => $request->toppings ?? [],
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

    public function checkout()
    {
        $cart     = session('cart', []);
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        return view('client.cart', compact('cart', 'subtotal'));
    }

    public function placeOrder(Request $request)
    {
        // TODO: Sprint 2 — tạo order thật với DB transaction
        session()->forget('cart');
        return redirect()->route('client.home')->with('success', 'Đặt hàng thành công!');
    }
}
