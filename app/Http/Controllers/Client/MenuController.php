<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems  = Product::with('category', 'options.values')
            ->where('is_active', true)
            ->get();

        $categories = Category::orderBy('priority')->get();

        return view('client.menu', compact('menuItems', 'categories'));
    }
}
