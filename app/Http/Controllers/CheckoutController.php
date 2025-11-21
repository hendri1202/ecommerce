<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(protected ShippingService $shippingService)
    {
    }

    public function index()
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.orders.index');
        }

        $items = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $subtotal = $items->sum(fn ($item) => $item->subtotal);
        $totalWeight = $items->sum(fn ($item) => $item->total_weight);
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->get();

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'totalWeight' => $totalWeight,
            'addresses' => $addresses,
        ]);
    }

    public function getCities(Request $request)
    {
        $request->validate([
            'province_id' => ['required'],
        ]);

        $cities = $this->shippingService->getCities($request->string('province_id'));

        return response()->json($cities);
    }

    public function getShippingOptions(Request $request)
    {
        $validated = $request->validate([
            'origin' => ['required', 'string'],
            'destination' => ['required', 'string'],
            'weight' => ['required', 'integer', 'min:1'],
            'courier' => ['required', 'string'],
        ]);

        $options = $this->shippingService->calculateShipping(
            $validated['origin'],
            $validated['destination'],
            $validated['weight'],
            $validated['courier'],
        );

        return response()->json($options);
    }

    public function store(CheckoutRequest $request)
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.orders.index');
        }

        $items = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $subtotal = $items->sum(fn ($item) => $item->subtotal);
        $totalWeight = $items->sum(fn ($item) => $item->total_weight);

        $shippingCost = (float) $request->input('shipping_cost', 0);
        $service = (string) $request->input('service', '');
        $address = auth()->user()->addresses()->where('id', $request->address_id)->first();

        if (!$address) {
            return redirect()->route('profile.edit')->with('error', 'Alamat tidak ditemukan. Silakan tambah alamat terlebih dahulu.');
        }

        try {
            DB::transaction(function () use ($request, $items, $subtotal, $shippingCost, $service, $address) {
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'code' => 'ORD-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(4)),
                    'recipient_name' => $address->recipient_name,
                    'phone' => $address->phone ?? '',
                    'address' => $address->address ?? '',
                    'province' => $address->province ?? '',
                    'city' => $address->city ?? '',
                    'postal_code' => $address->postal_code ?? '',
                    'courier' => $request->courier,
                    'service' => $service,
                    'shipping_cost' => $shippingCost,
                    'subtotal' => $subtotal,
                    'total' => $subtotal + $shippingCost,
                    'status' => 'pending',
                ]);

                foreach ($items as $cartItem) {
                    // Lock produk untuk memastikan stok tidak minus
                    $product = Product::lockForUpdate()->find($cartItem->product_id);
                    if (!$product || $product->stock < $cartItem->qty) {
                        throw new \RuntimeException('Stok tidak mencukupi untuk produk: ' . ($cartItem->product->name ?? ''));
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $product->price,
                        'qty' => $cartItem->qty,
                        'weight' => $product->weight,
                        'subtotal' => $product->price * $cartItem->qty,
                    ]);

                    $product->decrement('stock', $cartItem->qty);
                }

                Cart::where('user_id', auth()->id())->delete();
            });
        } catch (\Throwable $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        return redirect()->route('orders.history')->with('success', 'Order berhasil dibuat.');
    }
}
