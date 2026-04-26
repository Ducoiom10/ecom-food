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

        return view('client.product', compact('product'));
    }
}
