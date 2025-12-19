<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $orders = $user->orders()->orderByDesc('order_id')->get();

        $orderItems = [];
        if ($orders->isNotEmpty()) {
            $orderIds = $orders->pluck('order_id')->toArray();
            $items = \App\Models\OrderItem::whereIn('order_id', $orderIds)
                ->with('product')
                ->get()
                ->groupBy('order_id');

            foreach ($items as $orderId => $orderItemsGroup) {
                $orderItems[$orderId] = $orderItemsGroup->toArray();
            }
        }

        return view('profile', compact('user', 'orders', 'orderItems'));
    }
}
