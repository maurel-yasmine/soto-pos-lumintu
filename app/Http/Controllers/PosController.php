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

        // Semua proses dibungkus transaksi DB (kalau ada error, semua dibatalkan)
        $trx = DB::transaction(function () use ($data) {

            // Hitung ulang total DARI DATABASE (jangan percaya harga dari browser — praktik keamanan)
            $subtotal = 0;
            $detailRows = [];
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $lineSubtotal = $product->price * $item['qty'];
                $subtotal += $lineSubtotal;
                $detailRows[] = [
                    'product'  => $product,
                    'qty'      => $item['qty'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $total = $subtotal; // (diskon bisa ditambah nanti)

            // Buat kode transaksi unik: TRX-YYYYMMDD-XXXX
            $code = 'TRX-' . now()->format('Ymd') . '-' . str_pad(Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // 1) Simpan header transaksi
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

            // 2) Simpan tiap item + kurangi stok + catat inventory movement
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

                // Kurangi stok
                $product->decrement('stock', $row['qty']);

                // Catat pergerakan stok (audit trail)
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

        // Kirim balik ID transaksi (untuk diarahkan ke struk di STEP 8C)
        return response()->json([
            'success' => true,
            'transaction_id' => $trx->id,
            'transaction_code' => $trx->transaction_code,
        ]);
    }
}
