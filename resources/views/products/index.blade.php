<x-pos-layout title="Produk">

    @if (session('success'))
        <div class="mb-4 bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari produk..."
                class="border border-green-200 rounded-lg px-3 py-2 text-sm">
            <select name="category_id" class="border border-green-200 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <button class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600">Cari</button>
        </form>
        <a href="{{ route('products.create') }}"
           class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600">+ Tambah Produk</a>
    </div>

    <div class="bg-white rounded-xl border border-green-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-green-50 text-green-700 text-left">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Modal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr class="border-t border-green-50">
                        <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                        <td class="px-4 py-3">{{ $p->category->name ?? '-' }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if ($p->is_active)
                                <span class="text-green-600">Aktif</span>
                            @else
                                <span class="text-gray-400">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('products.edit', $p) }}" class="text-blue-500 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $p) }}"
                                  onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-pos-layout>
