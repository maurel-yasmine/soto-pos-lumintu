<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('name')->get();
        $movements = InventoryMovement::with('product')->orderByDesc('created_at')->limit(30)->get();

        return view('inventory.index', compact('products', 'movements'));
    }

    public function restock(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product->increment('stock', $data['quantity']);

        InventoryMovement::create([
            'product_id' => $product->id,
            'type'       => 'in',
            'quantity'   => $data['quantity'],
            'reference'  => 'Restock manual',
            'created_at' => now(),
        ]);

        return redirect()->route('inventory.index')->with('success', 'Stok ' . $product->name . ' berhasil ditambah.');
    }
}
