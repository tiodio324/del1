<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::all();
        $query = Product::query();

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $products = $query->get();

        return view('catalog', compact('categories', 'products'));
    }

    public function show($id): View
    {
        $product = Product::findOrFail($id);
        return view('product', compact('product'));
    }
}
