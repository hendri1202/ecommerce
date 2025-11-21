<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.products.index');
        }

        $items = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        $subtotal = $items->sum(fn ($item) => $item->subtotal);
        $totalWeight = $items->sum(fn ($item) => $item->total_weight);

        return view('cart.index', compact('items', 'subtotal', 'totalWeight'));
    }

    public function store(Request $request)
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.products.index');
        }

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $message = 'Produk ditambahkan ke keranjang.';

        DB::transaction(function () use ($validated, &$message) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

            $cart = Cart::firstOrNew([
                'user_id' => auth()->id(),
                'product_id' => $validated['product_id'],
            ]);

            $desiredTotal = ($cart->exists ? $cart->qty : 0) + $validated['qty'];
            $finalQty = min($desiredTotal, $product->stock);

            if ($finalQty <= 0) {
                throw new \RuntimeException('Stok produk habis.');
            }

            if ($finalQty < $desiredTotal) {
                $message = 'Stok terbatas, jumlah disesuaikan ke ' . $finalQty . '.';
            }

            $cart->qty = $finalQty;
            $cart->save();
        });

        return redirect()->route('cart.index')->with('success', $message);
    }

    public function update(Request $request, Cart $cart)
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.products.index');
        }

        abort_if($cart->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:0'],
        ]);

        if ($validated['qty'] === 0) {
            $cart->delete();
            return redirect()->route('cart.index');
        }

        $product = Product::findOrFail($cart->product_id);
        $finalQty = min($validated['qty'], $product->stock);
        $message = null;

        if ($finalQty <= 0) {
            $cart->delete();
            return redirect()->route('cart.index')->with('error', 'Stok produk habis, item dihapus dari keranjang.');
        }

        if ($finalQty < $validated['qty']) {
            $message = 'Stok terbatas, jumlah disesuaikan ke ' . $finalQty . '.';
        }

        $cart->update(['qty' => $finalQty]);

        return redirect()->route('cart.index')->with($message ? 'error' : 'success', $message ?? 'Jumlah diperbarui.');
    }

    public function destroy(Cart $cart)
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.products.index');
        }

        abort_if($cart->user_id !== auth()->id(), 403);

        $cart->delete();

        return redirect()->route('cart.index')->with('success', 'Item dihapus dari keranjang.');
    }
}
