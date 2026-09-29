<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filter periode: today / week / month (default month)
        $period = $request->get('period', 'month');

        $query = Transaction::query();
        if ($period === 'today') {
            $query->whereDate('transaction_date', today());
        } elseif ($period === 'week') {
            $query->whereBetween('transaction_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } else {
            $query->whereMonth('transaction_date', now()->month)
                  ->whereYear('transaction_date', now()->year);
        }

        $transactions = (clone $query)->get();
        $trxIds = $transactions->pluck('id');

        // ===== KPI =====
        $totalRevenue = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();

        $details = TransactionDetail::whereIn('transaction_id', $trxIds)->get();
        $itemsSold = $details->sum('quantity');
        $totalCost = $details->sum(fn($d) => $d->cost_price * $d->quantity);
        $totalProfit = $totalRevenue - $totalCost;
        $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // ===== Grafik: Revenue trend (per hari) =====
        $trend = (clone $query)
            ->select(DB::raw('DATE(transaction_date) as tgl'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('tgl')->orderBy('tgl')->get();

        // ===== Top 5 produk (by quantity) =====
        $topProducts = TransactionDetail::whereIn('transaction_id', $trxIds)
            ->select('product_id', DB::raw('SUM(quantity) as qty'))
            ->with('product')
            ->groupBy('product_id')->orderByDesc('qty')->limit(5)->get();

        // ===== Revenue per kategori =====
        $revenueByCategory = TransactionDetail::whereIn('transaction_id', $trxIds)
            ->join('products', 'products.id', '=', 'transaction_details.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.name', DB::raw('SUM(transaction_details.subtotal) as total'))
            ->groupBy('categories.name')->get();

        // ===== Metode pembayaran =====
        $byPayment = (clone $query)
            ->join('payment_methods', 'payment_methods.id', '=', 'transactions.payment_method_id')
            ->select('payment_methods.name', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_methods.name')->get();

        return view('dashboard', compact(
            'period', 'totalRevenue', 'totalTransactions', 'itemsSold',
            'totalProfit', 'avgTransaction', 'profitMargin',
            'trend', 'topProducts', 'revenueByCategory', 'byPayment'
        ));
    }
}
