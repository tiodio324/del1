<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $products = [];
        $totalPrice = 0;

        if (!empty($cart)) {
            $ids = array_keys($cart);
            $dbProducts = Product::whereIn('id', $ids)->get()->keyBy('id');

            foreach ($cart as $productId => $quantity) {
                if (isset($dbProducts[$productId])) {
                    $product = $dbProducts[$productId];
                    $sum = $product->price * $quantity;
                    $products[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'image' => $product->image,
                        'quantity' => $quantity,
                        'sum' => $sum,
                    ];
                    $totalPrice += $sum;
                }
            }
        }

        return view('cart', compact('products', 'totalPrice'));
    }

    public function add(Request $request): RedirectResponse
    {
        $productId = (int)$request->id;
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]++;
        } else {
            $cart[$productId] = 1;
        }

        session()->put('cart', $cart);

        return redirect()->back();
    }

    public function remove(Request $request): RedirectResponse
    {
        $productId = (int)$request->id;
        $cart = session()->get('cart', []);

        unset($cart[$productId]);

        session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }
}
