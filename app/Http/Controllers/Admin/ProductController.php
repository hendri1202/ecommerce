<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('sort')) {
            $sort = $request->sort;
            if ($sort === 'name_asc') {
                $query->orderBy('name');
            } elseif ($sort === 'name_desc') {
                $query->orderByDesc('name');
            } elseif ($sort === 'date_asc') {
                $query->orderBy('published_at')->orderBy('created_at');
            } elseif ($sort === 'date_desc') {
                $query->orderByDesc('published_at')->orderByDesc('created_at');
            } elseif ($sort === 'status') {
                $query->orderByDesc('is_active');
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $products = $query->paginate(20)->withQueryString();
        $recentOrders = Order::with('user')->orderByDesc('created_at')->limit(5)->get();

        return view('admin.products.index', compact('products', 'recentOrders'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.form', ['product' => new Product(), 'categories' => $categories]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['published_at'] = $request->input('published_at');

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk dibuat.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['published_at'] = $request->input('published_at');

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk diupdate.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk dihapus.');
    }
}
