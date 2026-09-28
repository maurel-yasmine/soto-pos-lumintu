<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Kategori
        $makanan = DB::table('categories')->insertGetId(['name' => 'Makanan', 'created_at' => now(), 'updated_at' => now()]);
        $minuman = DB::table('categories')->insertGetId(['name' => 'Minuman', 'created_at' => now(), 'updated_at' => now()]);
        $snack   = DB::table('categories')->insertGetId(['name' => 'Snack',   'created_at' => now(), 'updated_at' => now()]);

        // 2) Metode pembayaran
        foreach (['Cash', 'QRIS', 'Debit', 'E-Wallet'] as $pm) {
            DB::table('payment_methods')->insert(['name' => $pm, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        }

        // 3) Produk (cost_price = perkiraan modal ~65% harga jual, bisa diedit nanti)
        $products = [
            // MAKANAN
            [$makanan, 'Soto Ayam Kampung', 12000],
            [$makanan, 'Soto Ayam Kampung + Nasi', 15000],
            [$makanan, 'Soto Daging', 12000],
            [$makanan, 'Soto Daging + Nasi', 15000],
            [$makanan, 'Soto Ayam + Ceker', 15000],
            [$makanan, 'Soto Ayam + Ceker + Nasi', 18000],
            [$makanan, 'Soto Ayam + Leher', 15000],
            [$makanan, 'Soto Ayam + Leher + Nasi', 18000],
            [$makanan, 'Soto Ayam + Kepala', 15000],
            [$makanan, 'Soto Ayam + Kepala + Nasi', 18000],
            [$makanan, 'Rawon', 17000],
            [$makanan, 'Rawon + Nasi', 20000],
            [$makanan, 'Ayam Goreng Telur', 12000],
            [$makanan, 'Ayam Goreng Telur + Nasi', 15000],
            [$makanan, 'Ayam Goreng Pejantan / Kampung', 17000],
            [$makanan, 'Ayam G. Pejantan / Kampung + Nasi', 20000],
            // MINUMAN
            [$minuman, 'Kopi Hitam', 5000],
            [$minuman, 'Kopi Susu', 5000],
            [$minuman, 'White Coffee', 5000],
            [$minuman, 'Teh Manis', 3000],
            [$minuman, 'Es Teh Manis', 3000],
            [$minuman, 'Teh Tawar', 2000],
            [$minuman, 'Lemon Tea', 5000],
            [$minuman, 'Cendol', 5000],
            [$minuman, 'Teh Tarik', 5000],
            // SNACK
            [$snack, 'Tempe Goreng', 2000],
            [$snack, 'Tahu Goreng', 2000],
            [$snack, 'Tahu Isi', 2000],
            [$snack, 'Bakwan', 2000],
            [$snack, 'Sosis Solo', 3000],
            [$snack, 'Risol Mayo', 3000],
            [$snack, 'Sate Puyuh', 3000],
            [$snack, 'Sate Usus', 2000],
            [$snack, 'Sate Ati Ampela', 2000],
            [$snack, 'Sate Kikil', 2000],
            [$snack, 'Telur Asin', 5000],
            [$snack, 'Emping', 5000],
            [$snack, 'Peyek Kacang / Rebon', 5000],
            [$snack, 'Kelanting', 2000],
            [$snack, 'Kelanting Udang', 2000],
            [$snack, 'Makaroni Pedas', 2000],
            [$snack, 'Kerupuk Bulat', 1000],
            [$snack, 'Bagelen', 1000],
            [$snack, 'Kerupuk Palembang', 5000],
        ];

        foreach ($products as [$catId, $name, $price]) {
            DB::table('products')->insert([
                'category_id'   => $catId,
                'name'          => $name,
                'price'         => $price,
                'cost_price'    => round($price * 0.65),
                'stock'         => 100,
                'minimum_stock' => 10,
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // 4) Akun admin & kasir
        DB::table('users')->insert([
            'name' => 'Admin', 'email' => 'admin@soto.test',
            'password' => Hash::make('password'), 'role' => 'admin',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'Kasir', 'email' => 'kasir@soto.test',
            'password' => Hash::make('password'), 'role' => 'cashier',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
