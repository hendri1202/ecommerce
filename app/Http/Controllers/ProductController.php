<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active();

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $products = $query->orderByDesc('created_at')->paginate(12);

        return view('home', compact('products'));
    }

    public function show(Product $product)
    {
        abort_if(!$product->is_active, 404);

        return view('products.show', compact('product'));
    }
}

