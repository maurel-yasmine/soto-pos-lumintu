<x-pos-layout title="Dashboard">

    {{-- Filter periode --}}
    <div class="flex gap-2 mb-6">
        <a href="?period=today" class="px-4 py-2 rounded-lg text-sm {{ $period === 'today' ? 'bg-green-500 text-white' : 'bg-white border border-green-200 text-gray-600' }}">Hari Ini</a>
        <a href="?period=week" class="px-4 py-2 rounded-lg text-sm {{ $period === 'week' ? 'bg-green-500 text-white' : 'bg-white border border-green-200 text-gray-600' }}">Minggu Ini</a>
        <a href="?period=month" class="px-4 py-2 rounded-lg text-sm {{ $period === 'month' ? 'bg-green-500 text-white' : 'bg-white border border-green-200 text-gray-600' }}">Bulan Ini</a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-gray-400 text-sm">Total Revenue</p>
            <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-gray-400 text-sm">Total Transaksi</p>
            <p class="text-2xl font-bold text-green-700">{{ number_format($totalTransactions, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-gray-400 text-sm">Items Terjual</p>
            <p class="text-2xl font-bold text-green-700">{{ number_format($itemsSold, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-gray-400 text-sm">Rata-rata Transaksi</p>
            <p class="text-2xl font-bold text-green-700">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-gray-400 text-sm">Total Profit</p>
            <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <p class="text-gray-400 text-sm">Profit Margin</p>
            <p class="text-2xl font-bold text-green-700">{{ number_format($profitMargin, 1) }}%</p>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <h3 class="font-semibold text-green-700 mb-3">Tren Revenue</h3>
            <canvas id="trendChart"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <h3 class="font-semibold text-green-700 mb-3">Top 5 Produk Terlaris</h3>
            <canvas id="topChart"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <h3 class="font-semibold text-green-700 mb-3">Revenue per Kategori</h3>
            <canvas id="catChart"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-green-100 p-5">
            <h3 class="font-semibold text-green-700 mb-3">Metode Pembayaran</h3>
            <canvas id="payChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const green = '#22c55e', blue = '#3b82f6', orange = '#f97316', purple = '#a855f7';

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($trend->pluck('tgl')) !!},
                datasets: [{ label: 'Revenue', data: {!! json_encode($trend->pluck('total')) !!}, borderColor: green, backgroundColor: green, tension: .3, fill: false }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('topChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProducts->map(fn($t) => $t->product->name ?? '-')) !!},
                datasets: [{ label: 'Qty', data: {!! json_encode($topProducts->pluck('qty')) !!}, backgroundColor: blue }]
            },
            options: { indexAxis: 'y', plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('catChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($revenueByCategory->pluck('name')) !!},
                datasets: [{ data: {!! json_encode($revenueByCategory->pluck('total')) !!}, backgroundColor: [green, blue, orange] }]
            }
        });

        new Chart(document.getElementById('payChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($byPayment->pluck('name')) !!},
                datasets: [{ data: {!! json_encode($byPayment->pluck('total')) !!}, backgroundColor: [green, blue, orange, purple] }]
            }
        });
    </script>

</x-pos-layout>
