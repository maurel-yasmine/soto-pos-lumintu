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
                <h2 class="font-bold text-green-700 mb-3">Keranjang</h2>

                <div id="cartItems" class="space-y-2 mb-4 max-h-96 overflow-y-auto">
                    <p class="text-gray-400 text-sm text-center py-6" id="cartEmpty">Keranjang kosong. Klik menu untuk menambah.</p>
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
                    <button onclick="bukaBayar()" id="btnBayar"
                        class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold hover:bg-green-600 disabled:bg-gray-300"
                        disabled>Bayar</button>
                </div>
            </div>
        </div>

    </div>

    <div id="modalBayar" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
        <div class="bg-gray-100 rounded-2xl p-6 w-96 shadow-2xl border-t-4 border-green-500">
            <h2 class="font-bold text-green-700 text-lg mb-4">Pembayaran</h2>

            <div class="flex justify-between text-lg font-bold mb-4 bg-white rounded-lg px-3 py-2">
                <span>Total</span>
                <span id="modalTotal" class="text-green-700">Rp 0</span>
            </div>

            <label class="block text-sm font-medium mb-1">Metode Pembayaran</label>
            <select id="metodeBayar" onchange="cekMetode()" class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm mb-3 bg-white">
                @foreach ($paymentMethods as $pm)
                    <option value="{{ $pm->id }}" data-nama="{{ strtolower($pm->name) }}">{{ $pm->name }}</option>
                @endforeach
            </select>

            <label class="block text-sm font-medium mb-1">Uang Dibayar</label>
            <input type="number" id="uangBayar" oninput="hitungKembalian()"
                class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm mb-3 bg-white" placeholder="0">

            <div class="flex justify-between text-sm mb-4">
                <span class="text-gray-500">Kembalian</span>
                <span id="kembalian" class="font-medium">Rp 0</span>
            </div>

            <div class="flex gap-2">
                <button onclick="tutupBayar()" class="flex-1 border border-gray-300 bg-white py-2 rounded-lg text-sm">Batal</button>
                <button onclick="prosesBayar()" id="btnProses" class="flex-1 bg-green-500 text-white py-2 rounded-lg text-sm hover:bg-green-600">Proses</button>
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

        function totalHargaCart() {
            return cart.reduce((sum, i) => sum + i.harga * i.qty, 0);
        }

        function renderCart() {
            const box = document.getElementById('cartItems');
            if (cart.length === 0) {
                box.innerHTML = '<p class="text-gray-400 text-sm text-center py-6">Keranjang kosong. Klik menu untuk menambah.</p>';
                document.getElementById('totalItem').innerText = '0';
                document.getElementById('totalHarga').innerText = 'Rp 0';
                document.getElementById('btnBayar').disabled = true;
                return;
            }
            let html = '', totalItem = 0;
            cart.forEach(i => {
                totalItem += i.qty;
                html += '<div class="flex items-center justify-between gap-2 border-b border-green-50 pb-2">'
                    + '<div class="flex-1"><p class="text-sm font-medium leading-tight">' + i.nama + '</p>'
                    + '<p class="text-xs text-gray-400">' + rupiah(i.harga) + '</p></div>'
                    + '<div class="flex items-center gap-2">'
                    + '<button onclick="ubahQty(' + i.id + ', -1)" class="w-6 h-6 rounded bg-green-100 text-green-700">-</button>'
                    + '<span class="text-sm w-5 text-center">' + i.qty + '</span>'
                    + '<button onclick="ubahQty(' + i.id + ', 1)" class="w-6 h-6 rounded bg-green-100 text-green-700">+</button>'
                    + '</div></div>';
            });
            box.innerHTML = html;
            document.getElementById('totalItem').innerText = totalItem;
            document.getElementById('totalHarga').innerText = rupiah(totalHargaCart());
            document.getElementById('btnBayar').disabled = false;
        }

        function filterKategori(kat) {
            document.querySelectorAll('.menu-item').forEach(function (el) {
                el.style.display = (kat === 'all' || el.dataset.kat === kat) ? '' : 'none';
            });
            document.querySelectorAll('.kat-btn').forEach(function (b) {
                b.className = (b.dataset.kat === kat)
                    ? 'kat-btn bg-green-500 text-white px-4 py-2 rounded-lg text-sm'
                    : 'kat-btn bg-white border border-green-200 text-gray-600 px-4 py-2 rounded-lg text-sm';
            });
        }

        function bukaBayar() {
            if (cart.length === 0) return;
            document.getElementById('modalTotal').innerText = rupiah(totalHargaCart());
            document.getElementById('uangBayar').value = '';
            document.getElementById('kembalian').innerText = 'Rp 0';
            const modal = document.getElementById('modalBayar');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            cekMetode();
        }

        function tutupBayar() {
            const modal = document.getElementById('modalBayar');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function cekMetode() {
            const select = document.getElementById('metodeBayar');
            const nama = select.options[select.selectedIndex].dataset.nama || '';
            const input = document.getElementById('uangBayar');
            const total = totalHargaCart();
            if (nama === 'cash') {
                input.disabled = false;
                input.value = '';
                document.getElementById('kembalian').innerText = 'Rp 0';
            } else {
                input.disabled = true;
                input.value = total;
                document.getElementById('kembalian').innerText = 'Rp 0';
            }
        }

        function hitungKembalian() {
            const bayar = parseInt(document.getElementById('uangBayar').value) || 0;
            const kembali = bayar - totalHargaCart();
            document.getElementById('kembalian').innerText = rupiah(kembali >= 0 ? kembali : 0);
        }

        function prosesBayar() {
            const bayar = parseInt(document.getElementById('uangBayar').value) || 0;
            if (bayar < totalHargaCart()) {
                alert('Uang dibayar kurang dari total!');
                return;
            }
            document.getElementById('btnProses').disabled = true;
            const token = document.querySelector('meta[name="csrf-token"]').content;

            fetch('{{ route('pos.checkout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_method_id: document.getElementById('metodeBayar').value,
                    payment_amount: bayar,
                    items: cart.map(function (i) { return { id: i.id, qty: i.qty }; })
                })
            })
            .then(async function (res) {
                const data = await res.json().catch(function () { return {}; });
                if (res.ok && data.success) {
                    window.location.href = '/receipt/' + data.transaction_id;
                } else {
                    alert('Gagal menyimpan. Status: ' + res.status + ' ' + (data.message || JSON.stringify(data)));
                    document.getElementById('btnProses').disabled = false;
                }
            })
            .catch(function (err) {
                alert('Error: ' + err);
                document.getElementById('btnProses').disabled = false;
            });
        }
    </script>

</x-pos-layout>
