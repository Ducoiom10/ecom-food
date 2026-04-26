<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(int $id)
    {
        $product = Product::with('options.values', 'category')
            ->where('is_active', true)
            ->findOrFail($id);

        $related = Product::with('category')
            ->where('is_active', true)
            ->where('id', '!=', $id)
            ->where('category_id', $product->category_id)
            ->take(2)
            ->get();

        return view('client.product', compact('product', 'related'));
    }
}
