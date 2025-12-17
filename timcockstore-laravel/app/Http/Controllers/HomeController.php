<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $popularProducts = Product::inRandomOrder()->limit(6)->get();
        $saleProducts = Product::inRandomOrder()->limit(6)->get();

        return view('index', compact('popularProducts', 'saleProducts'));
    }
}
