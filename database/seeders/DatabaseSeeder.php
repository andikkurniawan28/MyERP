<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\User;
use App\Models\Account;
use App\Models\Warehouse;
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
            ['code'=>'2000','name'=>'Hutang Usaha','category'=>'liability','description'=>'Kewajiban membayar kepada supplier','normal_balance'=>'credit'],
            ['code'=>'2100','name'=>'Hutang Bank','category'=>'liability','description'=>'Pinjaman dari bank','normal_balance'=>'credit'],
            ['code'=>'3000','name'=>'Modal Pemilik','category'=>'equity','description'=>'Investasi modal dari pemilik','normal_balance'=>'credit'],
            ['code'=>'4000','name'=>'Pendapatan Penjualan','category'=>'revenue','description'=>'Pendapatan dari penjualan barang atau jasa','normal_balance'=>'credit'],
            ['code'=>'5000','name'=>'Harga Pokok Penjualan','category'=>'cogs','description'=>'Biaya pokok barang yang dijual','normal_balance'=>'debit'],
            ['code'=>'6000','name'=>'Beban Gaji','category'=>'expense','description'=>'Pengeluaran untuk gaji karyawan','normal_balance'=>'debit'],
            ['code'=>'6100','name'=>'Beban Sewa','category'=>'expense','description'=>'Pengeluaran untuk biaya sewa','normal_balance'=>'debit'],
            ['code'=>'6200','name'=>'Beban Utilitas','category'=>'expense','description'=>'Biaya listrik, air, telepon, dan utilitas lain','normal_balance'=>'debit'],
        ]);

        Warehouse::insert([
            ['name' => 'Gudang A', 'address' => 'Jl. Raya Maju Mundur'],
            ['name' => 'Gudang B', 'address' => 'Jl. Raya Maju Mundur'],
        ]);
    }
}
