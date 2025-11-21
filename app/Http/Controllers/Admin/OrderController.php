<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderByDesc('created_at')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'user');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,shipped,completed,cancelled'],
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Status pesanan diupdate.');
    }

    public function sendMessage(Request $request, Order $order)
    {
        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $order->messages()->create([
            'user_id' => auth()->id(),
            'to_user_id' => $order->user_id,
            'body' => $data['body'],
            'is_read' => false,
        ]);

        return back()->with('success', 'Pesan terkirim ke pelanggan.');
    }
}
