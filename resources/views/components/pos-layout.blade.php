<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Soto Seger Solo Lumintu' }} — POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-green-50 text-gray-800 antialiased">
    <div class="min-h-screen flex">

        <aside class="w-64 bg-white border-r border-green-100 flex flex-col fixed h-full">
            <div class="h-16 flex items-center gap-2 px-5 border-b border-green-100">
                <div class="w-9 h-9 rounded-lg bg-green-500 text-white flex items-center justify-center font-bold">S</div>
                <div>
                    <p class="font-bold text-green-700 leading-tight">Soto Seger</p>
                    <p class="text-xs text-gray-400 leading-tight">Solo Lumintu</p>
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <a href="{{ url('/dashboard') }}" class="block px-3 py-2.5 rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-700 transition">Dashboard</a>
                <a href="{{ url('/pos') }}" class="block px-3 py-2.5 rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-700 transition">POS / Kasir</a>
                <a href="{{ url('/products') }}" class="block px-3 py-2.5 rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-700 transition">Produk</a>
                <a href="{{ url('/categories') }}" class="block px-3 py-2.5 rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-700 transition">Kategori</a>
                <a href="{{ url('/inventory') }}" class="block px-3 py-2.5 rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-700 transition">Inventory</a>
                <a href="{{ url('/transactions') }}" class="block px-3 py-2.5 rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-700 transition">Transaksi</a>
                <a href="{{ url('/users') }}" class="block px-3 py-2.5 rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-700 transition">Users</a>
                <a href="{{ url('/reports') }}" class="block px-3 py-2.5 rounded-lg text-gray-600 hover:bg-green-50 hover:text-green-700 transition">Reports</a>
            </nav>

            <div class="border-t border-green-100 p-4">
                <p class="text-sm font-medium">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-gray-400 mb-2">{{ Auth::user()->email ?? '' }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-sm text-red-500 hover:text-red-600">Logout</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 ml-64">
            <header class="h-16 bg-white border-b border-green-100 flex items-center px-6">
                <h1 class="text-lg font-semibold text-green-700">{{ $title ?? 'Dashboard' }}</h1>
            </header>
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>

    </div>
</body>
</html>
