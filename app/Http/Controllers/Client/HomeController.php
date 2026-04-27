<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Combo;
use App\Models\Catalog\Product;

class HomeController extends Controller
{
    public function index()
    {
        $menuItems = Product::with('category')
            ->where('is_active', true)
            ->orderByDesc('is_best_seller')
            ->take(6)
            ->get();

        $combos  = Combo::where('is_active', true)->get();
        $reviews = \App\Data\MockData::reviews();

        return view('client.home.index', compact('menuItems', 'combos', 'reviews'));
    }
}
