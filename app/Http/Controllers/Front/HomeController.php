<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // here can use take() or limit() they're the same performance
        $products = Product::with('category')
            ->latest()
            ->active()
            ->limit(8);

        return view('layouts.front', [
            'products' => $products,
        ]);
    }
}
