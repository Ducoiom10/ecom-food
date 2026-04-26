<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;

class MenuController extends Controller
{
    public function index()
    {
        $query = Product::with('category')->where('is_active', true);

        if ($search = request('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($cat = request('category')) {
            if ($cat !== 'all') {
                $query->whereHas('category', fn($q) => $q->where('slug', $cat));
            }
        }

        $query->when(request('sort', 'popular'), fn($q, $sort) => match($sort) {
            'price_asc'  => $q->orderBy('base_price'),
            'price_desc' => $q->orderByDesc('base_price'),
            default      => $q->orderByDesc('is_best_seller')->orderByDesc('is_new'),
        });

        $menuItems = $query->get();

        return view('client.menu', compact('menuItems'));
    }
}
