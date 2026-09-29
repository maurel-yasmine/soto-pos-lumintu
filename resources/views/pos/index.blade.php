<x-pos-layout title="POS / Kasir">

    <div class="grid grid-cols-3 gap-4">

        <div class="col-span-2 max-h-[calc(100vh-160px)] overflow-y-auto pr-2">
            <div class="flex gap-2 mb-4 flex-wrap">
                <button onclick="filterKategori('all')" class="kat-btn bg-green-500 text-white px-4 py-2 rounded-lg text-sm" data-kat="all">Semua</button>
                @foreach ($categories as $c)
                    <button onclick="filterKategori('{{ $c->id }}')" class="kat-btn bg-white border border-green-200 text-gray-600 px-4 py-2 rounded-lg text-sm" data-kat="{{ $c->id }}">{{ $c->name }}</button>
                @endforeach
            </div>

            <div class="grid grid-cols-4 gap-2" id="menuGrid">
                @foreach ($products as $p)
                    <button
                        onclick="tambahKeCart({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->price }})"
                        class="menu-item bg-white border border-green-100 rounded-xl p-3 text-left hover:border-green-400 hover:shadow transition"
                        data-kat="{{ $p->category_id }}">
                        <p class="font-medium text-sm text-gray-800 leading-tight mb-1">{{ $p->name }}</p>
                        <p class="text-green-600 font-semibold text-sm">Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="col-span-1">
            <div class="bg-white rounded-xl border border-green-100 p-4 sticky top-6">
                <h2 class="font-bold text-green-700 mb-3">🛒 Keranjang</h2>

                <div id="cartItems" class="space-y-2 mb-4 max-h-96 overflow-y-auto">
                    <p class="text-gray-400 text-sm text-center py-6" id="cartEmpty">Keranjang kosong.<br>Klik menu untuk menambah.</p>
                </div>

                <div class="border-t border-green-100 pt-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500">Total Item</span>
                        <span id="totalItem" class="font-medium">0</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-green-700 mb-4">
                        <span>Total</span>
                        <span id="totalHarga">Rp 0</span>
                    </div>
                    <button onclick="checkout()" id="btnBayar"
                        class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold hover:bg-green-600 disabled:bg-gray-300"
                        disabled>Bayar</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        let cart = [];

        function tambahKeCart(id, nama, harga) {
            const item = cart.find(i => i.id === id);
            if (item) { item.qty++; } else { cart.push({ id, nama, harga, qty: 1 }); }
            renderCart();
        }

        function ubahQty(id, delta) {
            const item = cart.find(i => i.id === id);
            if (!item) return;
            item.qty += delta;
            if (item.qty <= 0) { cart = cart.filter(i => i.id !== id); }
            renderCart();
        }

        function rupiah(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

        function renderCart() {
            const box = document.getElementById('cartItems');
            if (cart.length === 0) {
                box.innerHTML = '<p class="text-gray-400 text-sm text-center py-6">Keranjang kosong.<br>Klik menu untuk menambah.</p>';
                document.getElementById('totalItem').innerText = '0';
                document.getElementById('totalHarga').innerText = 'Rp 0';
                document.getElementById('btnBayar').disabled = true;
                return;
            }
            let html = '', totalHarga = 0, totalItem = 0;
            cart.forEach(i => {
                const subtotal = i.harga * i.qty;
                totalHarga += subtotal; totalItem += i.qty;
                html += `
                    <div class="flex items-center justify-between gap-2 border-b border-green-50 pb-2">
                        <div class="flex-1">
                            <p class="text-sm font-medium leading-tight">${i.nama}</p>
                            <p class="text-xs text-gray-400">${rupiah(i.harga)}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="ubahQty(${i.id}, -1)" class="w-6 h-6 rounded bg-green-100 text-green-700">-</button>
                            <span class="text-sm w-5 text-center">${i.qty}</span>
                            <button onclick="ubahQty(${i.id}, 1)" class="w-6 h-6 rounded bg-green-100 text-green-700">+</button>
                        </div>
                    </div>`;
            });
            box.innerHTML = html;
            document.getElementById('totalItem').innerText = totalItem;
            document.getElementById('totalHarga').innerText = rupiah(totalHarga);
            document.getElementById('btnBayar').disabled = false;
        }

        function filterKategori(kat) {
            document.querySelectorAll('.menu-item').forEach(el => {
                el.style.display = (kat === 'all' || el.dataset.kat === kat) ? '' : 'none';
            });
            document.querySelectorAll('.kat-btn').forEach(b => {
                b.className = (b.dataset.kat === kat)
                    ? 'kat-btn bg-green-500 text-white px-4 py-2 rounded-lg text-sm'
                    : 'kat-btn bg-white border border-green-200 text-gray-600 px-4 py-2 rounded-lg text-sm';
            });
        }

        function checkout() {
            alert('Checkout akan kita buat di STEP 8 (simpan transaksi + struk).');
        }
    </script>

</x-pos-layout>
