<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Combo;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $menuItems = Product::with('category')
            ->where('is_active', true)
            ->orderByDesc('is_best_seller')
            ->get();

        $combos  = Combo::where('is_active', true)->get();
        $reviews = \App\Data\MockData::reviews(); // TODO: thay bằng DB

        return view('client.home', compact('menuItems', 'combos', 'reviews'));
    }
}
