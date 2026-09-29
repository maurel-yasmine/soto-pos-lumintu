<x-pos-layout title="Inventory">

    @if (session('success'))
        <div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel stok --}}
    <div class="bg-white rounded-xl border border-green-100 overflow-hidden mb-6">
        <div class="px-4 py-3 bg-green-50 font-semibold text-green-700">Stok Produk</div>
        <table class="w-full text-sm">
            <thead class="bg-green-50 text-green-700 text-left">
                <tr>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3 text-center">Stok</th>
                    <th class="px-4 py-3 text-center">Min</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Restock</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $p)
                    <tr class="border-t border-green-50">
                        <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                        <td class="px-4 py-3">{{ $p->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">{{ $p->stock }}</td>
                        <td class="px-4 py-3 text-center">{{ $p->minimum_stock }}</td>
                        <td class="px-4 py-3">
                            @if ($p->stock <= 0)
                                <span class="text-red-600 font-medium">Habis</span>
                            @elseif ($p->stock <= $p->minimum_stock)
                                <span class="text-orange-500 font-medium">Low Stock</span>
                            @else
                                <span class="text-green-600">Normal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('inventory.restock', $p) }}" class="flex gap-1">
                                @csrf
                                <input type="number" name="quantity" min="1" value="10"
                                    class="w-16 border border-green-200 rounded px-2 py-1 text-xs">
                                <button class="bg-green-500 text-white px-2 py-1 rounded text-xs hover:bg-green-600">+ Tambah</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Riwayat pergerakan stok --}}
    <div class="bg-white rounded-xl border border-green-100 overflow-hidden">
        <div class="px-4 py-3 bg-green-50 font-semibold text-green-700">Riwayat Pergerakan Stok (30 terakhir)</div>
        <table class="w-full text-sm">
            <thead class="bg-green-50 text-green-700 text-left">
                <tr>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3 text-center">Jumlah</th>
                    <th class="px-4 py-3">Referensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $m)
                    <tr class="border-t border-green-50">
                        <td class="px-4 py-3">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $m->product->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if ($m->type === 'in')
                                <span class="text-green-600">Masuk</span>
                            @elseif ($m->type === 'out')
                                <span class="text-red-500">Keluar</span>
                            @else
                                <span class="text-gray-500">Penyesuaian</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">{{ $m->quantity }}</td>
                        <td class="px-4 py-3">{{ $m->reference ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada pergerakan stok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-pos-layout>
