<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('pos.index', compact('categories', 'products', 'paymentMethods'));
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_amount'    => 'required|numeric|min:0',
            'items'             => 'required|array|min:1',
            'items.*.id'        => 'required|exists:products,id',
            'items.*.qty'       => 'required|integer|min:1',
        ]);

        $trx = DB::transaction(function () use ($data) {
            $subtotal = 0;
            $detailRows = [];
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $lineSubtotal = $product->price * $item['qty'];
                $subtotal += $lineSubtotal;
                $detailRows[] = ['product' => $product, 'qty' => $item['qty'], 'subtotal' => $lineSubtotal];
            }

            $total = $subtotal;
            $code = 'TRX-' . now()->format('Ymd') . '-' . str_pad(Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'transaction_code'  => $code,
                'user_id'           => auth()->id(),
                'payment_method_id' => $data['payment_method_id'],
                'transaction_date'  => now(),
                'subtotal'          => $subtotal,
                'discount'          => 0,
                'total_amount'      => $total,
                'payment_amount'    => $data['payment_amount'],
                'change_amount'     => max(0, $data['payment_amount'] - $total),
            ]);

            foreach ($detailRows as $row) {
                $product = $row['product'];
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $product->id,
                    'quantity'       => $row['qty'],
                    'price'          => $product->price,
                    'cost_price'     => $product->cost_price,
                    'subtotal'       => $row['subtotal'],
                ]);
                $product->decrement('stock', $row['qty']);
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type'       => 'out',
                    'quantity'   => $row['qty'],
                    'reference'  => $code,
                    'created_at' => now(),
                ]);
            }

            return $transaction;
        });

        return response()->json([
            'success' => true,
            'transaction_id' => $trx->id,
            'transaction_code' => $trx->transaction_code,
        ]);
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load('details.product', 'user', 'paymentMethod');
        return view('pos.receipt', compact('transaction'));
    }
}
