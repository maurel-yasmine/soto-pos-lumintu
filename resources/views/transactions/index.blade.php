<x-pos-layout title="Riwayat Transaksi">

    <form method="GET" class="flex gap-2 mb-4 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari kode transaksi..."
            class="border border-green-200 rounded-lg px-3 py-2 text-sm">
        <input type="date" name="date" value="{{ request('date') }}"
            class="border border-green-200 rounded-lg px-3 py-2 text-sm">
        <select name="payment_method_id" class="border border-green-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Semua Metode</option>
            @foreach ($paymentMethods as $pm)
                <option value="{{ $pm->id }}" @selected(request('payment_method_id') == $pm->id)>{{ $pm->name }}</option>
            @endforeach
        </select>
        <button class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600">Filter</button>
        <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-lg text-sm border border-gray-200">Reset</a>
    </form>

    <div class="bg-white rounded-xl border border-green-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-green-50 text-green-700 text-left">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Kasir</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $t)
                    <tr class="border-t border-green-50">
                        <td class="px-4 py-3 font-medium">{{ $t->transaction_code }}</td>
                        <td class="px-4 py-3">{{ $t->transaction_date->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $t->user->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $t->paymentMethod->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('pos.receipt', $t) }}" target="_blank" class="text-green-600 hover:underline">Lihat Struk</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>

</x-pos-layout>
