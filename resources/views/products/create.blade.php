<x-pos-layout title="Tambah Produk">

    <div class="bg-white rounded-xl border border-green-100 p-6 max-w-lg">
        <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <select name="category_id" class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm">
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nama Produk</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Harga Jual</label>
                    <input type="number" name="price" value="{{ old('price', 0) }}"
                        class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Harga Modal</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', 0) }}"
                        class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}"
                        class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Stok Minimum</label>
                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', 10) }}"
                        class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" checked> Produk aktif
            </label>

            <div class="flex gap-2 pt-2">
                <button class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600">Simpan</button>
                <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-lg text-sm border border-gray-200">Batal</a>
            </div>
        </form>
    </div>

</x-pos-layout>
