<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderHistoryController extends Controller
{
    public function index()
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.orders.index');
        }

        $orders = Order::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.orders.show', $order);
        }

        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('items');

        return view('orders.show', compact('order'));
    }
}
