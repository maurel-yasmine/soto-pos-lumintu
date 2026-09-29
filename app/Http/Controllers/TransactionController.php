<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user', 'paymentMethod');

        if ($request->filled('date')) {
            $query->whereDate('transaction_date', $request->date);
        }
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }
        if ($request->filled('search')) {
            $query->where('transaction_code', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->orderByDesc('transaction_date')->paginate(20)->withQueryString();
        $paymentMethods = PaymentMethod::all();

        return view('transactions.index', compact('transactions', 'paymentMethods'));
    }
}
