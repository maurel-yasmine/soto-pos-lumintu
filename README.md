# Soto Seger Solo Lumintu — POS & Business Intelligence System

Aplikasi Point of Sale (POS) berbasis web untuk warung makan Soto Seger Solo Lumintu,
lengkap dengan operational database, ETL pipeline, data warehouse, dan
business intelligence dashboard.

Proyek ini dibangun sebagai portofolio untuk peran Data Analyst / Data Engineer /
Business Intelligence, menunjukkan alur data end-to-end dari transaksi kasir sampai
analisis bisnis.

Dibuat oleh Maurel Chairinniswah Yasmine — Computer Science, BINUS University.

## Arsitektur

Frontend (Blade + Tailwind + JS) -> Laravel Backend (PHP) -> MySQL Operational Database (3NF) -> Pentaho ETL -> Data Warehouse (Star Schema) -> Power BI Dashboard

## Fitur

Aplikasi Kasir (POS):
- Login multi-role (Admin & Kasir)
- POS interface: pilih menu, keranjang, hitung total otomatis
- Pembayaran: Cash / QRIS / Debit / E-Wallet (dengan hitung kembalian)
- Struk digital + QR code + cetak/print
- Manajemen produk & kategori (CRUD)
- Manajemen inventory + low stock alert + audit trail
- Riwayat transaksi dengan filter

Business Intelligence:
- Dashboard KPI: Revenue, Transaksi, Items Sold, Avg Transaction, Profit, Profit Margin
- Grafik: tren revenue, produk terlaris, revenue per kategori, metode pembayaran
- Filter periode (harian/mingguan/bulanan)

## Tech Stack

- Frontend: HTML, Tailwind CSS, JavaScript, Chart.js
- Backend: Laravel (PHP)
- Database: MySQL
- ETL: Pentaho Data Integration
- Data Warehouse: MySQL (Star Schema)
- BI: Power BI

## Desain Database

Operational database ternormalisasi (3NF) dengan 7 tabel:
users, categories, products, payment_methods, transactions, transaction_details, inventory_movements.

Data warehouse memakai dimensional modeling (star schema):
DIM_DATE, DIM_PRODUCT, DIM_CATEGORY, DIM_PAYMENT_METHOD, DIM_CASHIER, FACT_SALES.

## Cara Menjalankan (Lokal)

Butuh: PHP, Composer, MySQL (via Laragon), Node.js.

    composer install
    npm install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed --seeder=MasterDataSeeder
    npm run build
    php artisan serve

Akun default:
- Admin: admin@soto.test / password
- Kasir: kasir@soto.test / password

## Business Questions yang Dijawab

Total revenue, jumlah transaksi, rata-rata transaksi, produk terlaris, kategori
paling menguntungkan, jam ramai, metode bayar terpopuler, profitabilitas produk,
low stock, tren revenue, dan analisis product performance.

## Status Pengembangan

## Status Pengembangan

- [x] Aplikasi POS (kasir, transaksi, struk, inventory)
- [x] Business analysis dashboard
- [x] Pentaho ETL - Data Warehouse
- [x] Power BI Dashboard
