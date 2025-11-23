<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = \App\Models\Order::whereIn('status', ['paid', 'shipped', 'completed'])->sum('total');
        $totalOrders = \App\Models\Order::count();
        $totalProducts = \App\Models\Product::count();
        $totalCustomers = \App\Models\User::where('role', 'customer')->count();

        $recentOrders = \App\Models\Order::with('user')->latest()->limit(5)->get();
        $lowStockProducts = \App\Models\Product::where('stock', '<', 10)->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'recentOrders',
            'lowStockProducts'
        ));
    }
}
