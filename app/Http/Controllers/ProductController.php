<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'cost_price'    => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
        ]);
        $data['is_active'] = $request->has('is_active');

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'cost_price'    => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
        ]);
        $data['is_active'] = $request->has('is_active');

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
