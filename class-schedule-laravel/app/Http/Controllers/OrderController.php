<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function showCheckout(): View
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        return view('checkout');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        $user = Auth::user();
        $totalAmount = 0;

        // Пересчёт суммы
        $stmt = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        foreach ($cart as $productId => $quantity) {
            if (isset($stmt[$productId])) {
                $totalAmount += $stmt[$productId]->price * $quantity;
            }
        }

        // Создание заказа
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $totalAmount,
            'status' => 'new',
        ]);

        // Сохранение товаров заказа
        foreach ($cart as $productId => $quantity) {
            if (isset($stmt[$productId])) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $stmt[$productId]->price,
                ]);
            }
        }

        // Очистка корзины
        session()->forget('cart');

        return redirect()->route('order.confirmation', ['order_id' => $order->order_id]);
    }

    public function confirmation($order_id): View
    {
        $user = Auth::user();
        $order = Order::where('order_id', $order_id)->where('user_id', $user->id)->firstOrFail();
        $items = OrderItem::where('order_id', $order_id)
            ->with('product')
            ->get();

        return view('order-confirmation', compact('order', 'items'));
    }
}
