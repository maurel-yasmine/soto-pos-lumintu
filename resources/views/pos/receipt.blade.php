<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->transaction_code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .receipt { background: #fff; width: 300px; margin: 0 auto; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        .center { text-align: center; }
        .line { border-top: 1px dashed #999; margin: 8px 0; }
        table { width: 100%; font-size: 12px; }
        td { padding: 2px 0; vertical-align: top; }
        .right { text-align: right; }
        h1 { font-size: 15px; margin: 0; font-weight: 700; }
        p { margin: 2px 0; font-size: 12px; }
        .total { font-size: 14px; font-weight: 700; }
        .qr { margin: 10px auto; display: block; }
        .actions { width: 300px; margin: 16px auto; display: flex; gap: 8px; }
        .actions button, .actions a { flex: 1; padding: 10px; border: 0; border-radius: 8px; font-size: 13px; cursor: pointer; text-align: center; text-decoration: none; font-family: inherit; }
        .btn-print { background: #22c55e; color: #fff; }
        .btn-back { background: #e5e7eb; color: #333; }
        @media print { .actions { display: none; } body { background: #fff; } }
    </style>
</head>
<body>

    <div class="receipt" id="receipt">
        <div class="center">
            <h1>SOTO SEGER SOLO LUMINTU</h1>
            <p>Terima kasih atas kunjungan Anda</p>
        </div>
        <div class="line"></div>

        <p>No: {{ $transaction->transaction_code }}</p>
        <p>Tgl: {{ $transaction->transaction_date->format('d/m/Y H:i') }}</p>
        <p>Kasir: {{ $transaction->user->name ?? '-' }}</p>
        <div class="line"></div>

        <table>
            @foreach ($transaction->details as $d)
                <tr>
                    <td>{{ $d->product->name ?? 'Produk' }}</td>
                </tr>
                <tr>
                    <td>{{ $d->quantity }} x {{ number_format($d->price, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
        <div class="line"></div>

        <table>
            <tr><td>Subtotal</td><td class="right">{{ number_format($transaction->subtotal, 0, ',', '.') }}</td></tr>
            <tr class="total"><td>TOTAL</td><td class="right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td></tr>
            <tr><td>Bayar ({{ $transaction->paymentMethod->name ?? '-' }})</td><td class="right">{{ number_format($transaction->payment_amount, 0, ',', '.') }}</td></tr>
            <tr><td>Kembali</td><td class="right">{{ number_format($transaction->change_amount, 0, ',', '.') }}</td></tr>
        </table>
        <div class="line"></div>

        @php
            $isiQR = "SOTO SEGER SOLO LUMINTU
"
                . "No: " . $transaction->transaction_code . "
"
                . "Tgl: " . $transaction->transaction_date->format('d/m/Y H:i') . "
"
                . "Total: Rp " . number_format($transaction->total_amount, 0, ',', '.') . "
"
                . "Item:
";
            foreach ($transaction->details as $d) {
                $isiQR .= "- " . ($d->product->name ?? 'Produk') . " x" . $d->quantity . "
";
            }
            $isiQR .= "Lokasi: https://share.google/V6PVsksdBx4gvtqJu";
        @endphp

        <div class="center">
            <img class="qr"
                src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data={{ urlencode($isiQR) }}"
                alt="QR Struk" width="130" height="130">
            <p>Scan untuk detail transaksi & lokasi</p>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('pos.index') }}" class="btn-back">Transaksi Baru</a>
        <button onclick="window.print()" class="btn-print">Print / Simpan PDF</button>
    </div>

</body>
</html>
