<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $customers = User::where('role', 'customer')->orderBy('name')->get();
            $customerId = request('customer_id');

            if (!$customerId && $customers->count()) {
                $customerId = $customers->first()->id;
            }

            $messagesQuery = Message::with('user', 'recipient', 'order')
                ->when($customerId, function ($q) use ($customerId, $user) {
                    $q->where(function ($inner) use ($customerId, $user) {
                        $inner->where('user_id', $customerId)->where('to_user_id', $user->id);
                    })->orWhere(function ($inner) use ($customerId, $user) {
                        $inner->where('user_id', $user->id)->where('to_user_id', $customerId);
                    });
                })
                ->latest();

            $messages = $messagesQuery->paginate(30);
            $orders = Order::with('user')->orderByDesc('created_at')->get();

            // tandai pesan dari customer ini sebagai read
            if ($customerId) {
                Message::where('user_id', $customerId)
                    ->where('to_user_id', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }

            // unread count per customer
            $unreadPerCustomer = Message::selectRaw('user_id, COUNT(*) as total')
                ->whereIn('user_id', $customers->pluck('id'))
                ->where('to_user_id', $user->id)
                ->where('is_read', false)
                ->groupBy('user_id')
                ->pluck('total', 'user_id');
        } else {
            $admin = User::where('role', 'admin')->first();
            $messages = Message::with('user', 'recipient', 'order')
                ->where(function ($q) use ($user, $admin) {
                    $q->where('user_id', $user->id)->where('to_user_id', $admin?->id);
                })->orWhere(function ($q) use ($user, $admin) {
                    $q->where('user_id', $admin?->id)->where('to_user_id', $user->id);
                })
                ->latest()
                ->paginate(30);
            $orders = Order::where('user_id', $user->id)->orderByDesc('created_at')->get();
            $customers = collect();
            $unreadPerCustomer = collect();

            if ($admin) {
                Message::where('user_id', $admin->id)
                    ->where('to_user_id', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
        }

        return view('messages.index', compact('messages', 'orders', 'customers', 'unreadPerCustomer'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'order_id' => ['nullable', 'exists:orders,id'],
            'body' => ['required', 'string'],
            'to_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $targetAdmin = User::where('role', 'admin')->first();

        // Tentukan penerima
        $toUserId = $validated['to_user_id'] ?? null;
        if (!$user->isAdmin()) {
            $toUserId = $targetAdmin?->id;
        } else {
            // admin wajib pilih customer jika belum ada
            if (!$toUserId) {
                return back()->with('error', 'Pilih penerima pesan (customer).');
            }
        }

        // Validasi kepemilikan order jika user biasa
        if (!$user->isAdmin() && !empty($validated['order_id'])) {
            $owned = Order::where('id', $validated['order_id'])
                ->where('user_id', $user->id)
                ->exists();
            if (!$owned) {
                return back()->with('error', 'Order tidak valid.');
            }
        }

        Message::create([
            'user_id' => $user->id,
            'to_user_id' => $toUserId,
            'order_id' => $validated['order_id'] ?? null,
            'body' => $validated['body'],
            'is_read' => false,
        ]);

        return back()->with('success', 'Pesan terkirim.');
    }
}
