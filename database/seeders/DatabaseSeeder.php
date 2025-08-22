<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Item;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Models\Account;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::insert([
            ['name' => 'Direktur'],
            ['name' => 'Manajer'],
            ['name' => 'Admin'],
        ]);

        User::insert([
            ['username' => 'direktur', 'password' => bcrypt('direktur'), 'name' => ucwords('Andik Kurniawan'), 'role_id' => 1],
            ['username' => 'manajer', 'password' => bcrypt('manajer'), 'name' => ucwords('Novia Celvi Aprilia'), 'role_id' => 2],
            ['username' => 'admin', 'password' => bcrypt('admin'), 'name' => ucwords('Adhyaksa Raga Dananta'), 'role_id' => 3],
        ]);

        Account::insert([
            ['code'=>'1000','name'=>'Kas','category'=>'asset','description'=>'Saldo kas yang tersedia','normal_balance'=>'debit'],
            ['code'=>'1010','name'=>'Bank','category'=>'asset','description'=>'Saldo rekening bank','normal_balance'=>'debit'],
            ['code'=>'1100','name'=>'Piutang Usaha','category'=>'asset','description'=>'Tagihan kepada pelanggan','normal_balance'=>'debit'],
            ['code'=>'1200','name'=>'Persediaan Barang','category'=>'asset','description'=>'Nilai persediaan barang dagang di gudang','normal_balance'=>'debit'],
            ['code'=>'2000','name'=>'Hutang Usaha','category'=>'liability','description'=>'Kewajiban membayar kepada supplier','normal_balance'=>'credit'],
            ['code'=>'2100','name'=>'Hutang Bank','category'=>'liability','description'=>'Pinjaman dari bank','normal_balance'=>'credit'],
            ['code'=>'3000','name'=>'Modal Pemilik','category'=>'equity','description'=>'Investasi modal dari pemilik','normal_balance'=>'credit'],
            ['code'=>'4000','name'=>'Pendapatan Penjualan','category'=>'revenue','description'=>'Pendapatan dari penjualan barang atau jasa','normal_balance'=>'credit'],
            ['code'=>'4100','name'=>'Pendapatan Selisih Persediaan','category'=>'revenue','description'=>'Pendapatan dari selisih persediaan positif','normal_balance'=>'credit'],
            ['code'=>'5000','name'=>'Harga Pokok Penjualan','category'=>'cogs','description'=>'Biaya pokok barang yang dijual','normal_balance'=>'debit'],
            ['code'=>'6000','name'=>'Beban Gaji','category'=>'expense','description'=>'Pengeluaran untuk gaji karyawan','normal_balance'=>'debit'],
            ['code'=>'6100','name'=>'Beban Sewa','category'=>'expense','description'=>'Pengeluaran untuk biaya sewa','normal_balance'=>'debit'],
            ['code'=>'6200','name'=>'Beban Utilitas','category'=>'expense','description'=>'Biaya listrik, air, telepon, dan utilitas lain','normal_balance'=>'debit'],
            ['code'=>'6300','name'=>'Beban Selisih Persediaan','category'=>'expense','description'=>'Biaya kerugian dari selisih persediaan negatif','normal_balance'=>'debit'],
        ]);

        Warehouse::insert([
            ['name' => 'Gudang A', 'address' => 'Jl. Raya Maju Mundur'],
            ['name' => 'Gudang B', 'address' => 'Jl. Raya Maju Mundur'],
        ]);

        Unit::insert([
            ['name' => 'Dus'],
            ['name' => 'Pack'],
            ['name' => 'Pcs'],
            ['name' => 'Kg'],
            ['name' => 'Gram'],
            ['name' => 'Liter'],
            ['name' => 'Meter'],
        ]);

        ItemCategory::insert([
            ['name' => 'Bahan Baku Utama'],
            ['name' => 'Bahan Penolong'],
            ['name' => 'Barang Setengah Jadi'],
            ['name' => 'Produk Jadi'],
            ['name' => 'Bahan Kemasan'],
            ['name' => 'Sparepart & Peralatan'],
            ['name' => 'Barang Dagang'],
        ]);

        Item::insert([
             [
                'item_category_id' => 1, 'barcode' => 'BBU-001', 'code' => 'ITM-001', 'name' => 'Gula Kristal Putih', 'description' => 'Bahan baku utama produksi, gula kristal putih curah',
                'main_unit_id' => 5, 'secondary_unit_id' => 4, 'conversion_rate' => 1000,
                'purchase_price_secondary' => 12000.00, 'selling_price_secondary' => null,
                'purchase_price_main' => 12.00, 'selling_price_main' => null
            ],
            [
                'item_category_id' => 5, 'barcode' => 'BK-001', 'code' => 'ITM-002', 'name' => 'Karung Plastik 50 Kg', 'description' => 'Kemasan plastik karung untuk produk jadi 50Kg',
                'main_unit_id' => 3, 'secondary_unit_id' => 2, 'conversion_rate' => 10,
                'purchase_price_secondary' => 150000.00, 'selling_price_secondary' => null,
                'purchase_price_main' => 15000.00, 'selling_price_main' => null
            ],
            [
                'item_category_id' => 6, 'barcode' => 'SP-001', 'code' => 'ITM-003', 'name' => 'Bearing Mesin', 'description' => 'Sparepart bearing untuk mesin produksi',
                'main_unit_id' => 3, 'secondary_unit_id' => 1, 'conversion_rate' => 20,
                'purchase_price_secondary' => 200000.00, 'selling_price_secondary' => null,
                'purchase_price_main' => 10000.00, 'selling_price_main' => null
            ],
            [
                'item_category_id' => 4, 'barcode' => 'PJ-001', 'code' => 'ITM-004', 'name' => 'Gula Premium 1 Kg', 'description' => 'Produk jadi gula kemasan premium 1Kg',
                'main_unit_id' => 4, 'secondary_unit_id' => 1, 'conversion_rate' => 20,
                'purchase_price_secondary' => 220000.00, 'selling_price_secondary' => 250000.00,
                'purchase_price_main' => 11000.00, 'selling_price_main' => 12500.00
            ],
            [
                'item_category_id' => 2, 'barcode' => 'BP-001', 'code' => 'ITM-005', 'name' => 'Kapur Tawas', 'description' => 'Bahan penolong untuk proses pemurnian',
                'main_unit_id' => 6, 'secondary_unit_id' => 7, 'conversion_rate' => 200,
                'purchase_price_secondary' => 1000000.00, 'selling_price_secondary' => null,
                'purchase_price_main' => 5000.00, 'selling_price_main' => null
            ],
        ]);

        Setting::insert([
            ['inventory_account_id' => 4, 'stock_in_account_id' => 9, 'stock_out_account_id' => 14],
        ]);
    }
}
