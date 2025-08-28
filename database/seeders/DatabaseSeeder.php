<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Item;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Models\Account;
use App\Models\Contact;
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
            ['username' => 'demo', 'password' => bcrypt('demo'), 'name' => ucwords('Andik Kurniawan'), 'role_id' => 1],
        ]);

        Account::insert([
            ['code'=>'1000','name'=>'Kas','category'=>'asset','description'=>'Saldo kas yang tersedia','normal_balance'=>'debit','is_payment_gateway'=>1],
            ['code'=>'1010','name'=>'Bank','category'=>'asset','description'=>'Saldo rekening bank','normal_balance'=>'debit','is_payment_gateway'=>1],
            ['code'=>'1100','name'=>'Piutang Usaha','category'=>'asset','description'=>'Tagihan kepada pelanggan','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'1200','name'=>'Persediaan Barang','category'=>'asset','description'=>'Nilai persediaan barang dagang di gudang','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'2000','name'=>'Hutang Usaha','category'=>'liability','description'=>'Kewajiban membayar kepada supplier','normal_balance'=>'credit','is_payment_gateway'=>0],
            ['code'=>'2100','name'=>'Hutang Bank','category'=>'liability','description'=>'Pinjaman dari bank','normal_balance'=>'credit','is_payment_gateway'=>0],
            ['code'=>'3000','name'=>'Modal Pemilik','category'=>'equity','description'=>'Investasi modal dari pemilik','normal_balance'=>'credit','is_payment_gateway'=>0],
            ['code'=>'4000','name'=>'Pendapatan Penjualan','category'=>'revenue','description'=>'Pendapatan dari penjualan barang atau jasa','normal_balance'=>'credit','is_payment_gateway'=>0],
            ['code'=>'4100','name'=>'Pendapatan Selisih Persediaan','category'=>'revenue','description'=>'Pendapatan dari selisih persediaan positif','normal_balance'=>'credit','is_payment_gateway'=>0],
            ['code'=>'5000','name'=>'Harga Pokok Penjualan','category'=>'cogs','description'=>'Biaya pokok barang yang dijual','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'6000','name'=>'Beban Gaji','category'=>'expense','description'=>'Pengeluaran untuk gaji karyawan','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'6100','name'=>'Beban Sewa','category'=>'expense','description'=>'Pengeluaran untuk biaya sewa','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'6200','name'=>'Beban Utilitas','category'=>'expense','description'=>'Biaya listrik, air, telepon, dan utilitas lain','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'6300','name'=>'Beban Selisih Persediaan','category'=>'expense','description'=>'Biaya kerugian dari selisih persediaan negatif','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'1300','name'=>'PPn Masukan','category'=>'asset','description'=>'Pajak Pertambahan Nilai (PPN) yang dibayarkan saat pembelian barang atau jasa dan dapat dikreditkan saat pelaporan pajak.','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'2200','name'=>'PPn Keluaran','category'=>'liability','description'=>'Pajak Pertambahan Nilai (PPN) yang dipungut dari pelanggan saat penjualan barang atau jasa, menjadi kewajiban perusahaan untuk disetor ke kantor pajak.','normal_balance'=>'credit','is_payment_gateway'=>0],
            ['code'=>'6400','name'=>'Beban Pengiriman','category'=>'expense','description'=>'Biaya pengiriman ke supplier/customer','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'4200','name'=>'Diskon Pembelian','category'=>'revenue','description'=>'Diskon yang didapat dari Pembelian','normal_balance'=>'credit','is_payment_gateway'=>0],
            ['code'=>'6500','name'=>'Diskon Penjualan','category'=>'expense','description'=>'Diskon yang diberikan dari Penjualan','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'6600','name'=>'Biaya Lain-lain','category'=>'expense','description'=>'Biaya lain dari pembelian/penjualan','normal_balance'=>'debit','is_payment_gateway'=>0],
            ['code'=>'4300','name'=>'Pendapatan Pengiriman','category'=>'revenue','description'=>'Pendapatan ongkir dari penjualan','normal_balance'=>'credit','is_payment_gateway'=>0],
            ['code'=>'4400','name'=>'Pendapatan Lain-lain','category'=>'revenue','description'=>'Pendapatan lain-lain','normal_balance'=>'credit','is_payment_gateway'=>0],
            ['code'=>'3100','name'=>'Laba Ditahan','category'=>'equity','description'=>'Laba ditahan','normal_balance'=>'credit','is_payment_gateway'=>0],
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
                'main_unit_id' => 5,
                // 'secondary_unit_id' => 4, 'conversion_rate' => 1000,
                // 'purchase_price_secondary' => 12000.00, 'selling_price_secondary' => null,
                'purchase_price_main' => 15000, 'selling_price_main' => 18000
            ],
            [
                'item_category_id' => 5, 'barcode' => 'BK-001', 'code' => 'ITM-002', 'name' => 'Karung Plastik 50 Kg', 'description' => 'Kemasan plastik karung untuk produk jadi 50Kg',
                'main_unit_id' => 3,
                // 'secondary_unit_id' => 2, 'conversion_rate' => 10,
                // 'purchase_price_secondary' => 150000.00, 'selling_price_secondary' => null,
                'purchase_price_main' => 15000.00, 'selling_price_main' => null
            ],
            [
                'item_category_id' => 6, 'barcode' => 'SP-001', 'code' => 'ITM-003', 'name' => 'Bearing Mesin', 'description' => 'Sparepart bearing untuk mesin produksi',
                'main_unit_id' => 3,
                // 'secondary_unit_id' => 1, 'conversion_rate' => 20,
                // 'purchase_price_secondary' => 200000.00, 'selling_price_secondary' => null,
                'purchase_price_main' => 10000.00, 'selling_price_main' => null
            ],
            [
                'item_category_id' => 4, 'barcode' => 'PJ-001', 'code' => 'ITM-004', 'name' => 'Gula Premium 1 Kg', 'description' => 'Produk jadi gula kemasan premium 1Kg',
                'main_unit_id' => 4,
                // 'secondary_unit_id' => 1, 'conversion_rate' => 20,
                // 'purchase_price_secondary' => 220000.00, 'selling_price_secondary' => 250000.00,
                'purchase_price_main' => 11000.00, 'selling_price_main' => 12500.00
            ],
            [
                'item_category_id' => 2, 'barcode' => 'BP-001', 'code' => 'ITM-005', 'name' => 'Kapur Tawas', 'description' => 'Bahan penolong untuk proses pemurnian',
                'main_unit_id' => 6,
                // 'secondary_unit_id' => 7, 'conversion_rate' => 200,
                // 'purchase_price_secondary' => 1000000.00, 'selling_price_secondary' => null,
                'purchase_price_main' => 5000.00, 'selling_price_main' => null
            ],
        ]);

        Setting::insert([
            [
                'inventory_account_id' => 4, 'stock_in_account_id' => 9, 'stock_out_account_id' => 14,
                'purchase_subtotal_account_id' => 4,
                'purchase_tax_account_id' => Account::where('code', 1300)->get()->last()->id,
                'purchase_freight_account_id' => Account::where('code', 6400)->get()->last()->id,
                'purchase_expenses_account_id' => Account::where('code', 6600)->get()->last()->id,
                'purchase_discount_account_id' => Account::where('code', 4200)->get()->last()->id,
                'purchase_grand_total_account_id' => Account::where('code', 2000)->get()->last()->id,
                'sales_subtotal_account_id' => Account::where('code', 4000)->get()->last()->id,
                'sales_tax_account_id' => Account::where('code', 2200)->get()->last()->id,
                'sales_freight_account_id' => Account::where('code', 4300)->get()->last()->id,
                'sales_expenses_account_id' => Account::where('code', 4400)->get()->last()->id,
                'sales_discount_account_id' => Account::where('code', 6500)->get()->last()->id,
                'sales_grand_total_account_id' => Account::where('code', 1100)->get()->last()->id,
                'sales_cogs_account_id' => Account::where('code', 5000)->get()->last()->id,
                'retained_earning_account_id' => Account::where('code', 3100)->get()->last()->id,
            ],
        ]);

        Contact::insert([
            [
                'name' => 'Andi Saputra', 'organization_name' => 'PT Sumber Makmur', 'position' => 'Purchasing Manager',
                'email' => 'andi.saputra@sumbermakmur.com', 'phone' => '081234567890', 'whatsapp' => '081234567890',
                'npwp' => '12.345.678.9-012.345', 'birthday' => '1985-06-15',
                'personal_address' => 'Jl. Merdeka No. 10, Jakarta', 'work_address' => 'Jl. Industri Raya No. 5, Jakarta',
                'type' => 'supplier', 'prefix' => 'Bapak',
            ],
            [
                'name' => 'Budi Santoso', 'organization_name' => 'CV Aneka Jaya', 'position' => 'Owner',
                'email' => 'budi@anekajaya.co.id', 'phone' => '082112345678', 'whatsapp' => '082112345678',
                'npwp' => '23.456.789.0-123.456', 'birthday' => '1978-09-21',
                'personal_address' => 'Jl. Diponegoro No. 12, Bandung', 'work_address' => 'Jl. Pasar Baru No. 2, Bandung',
                'type' => 'customer', 'prefix' => 'Bapak',
            ],
            [
                'name' => 'Citra Dewi', 'organization_name' => 'PT Sejahtera Bersama', 'position' => 'Finance Director',
                'email' => 'citra@sejahterabersama.com', 'phone' => '081398765432', 'whatsapp' => '081398765432',
                'npwp' => '34.567.890.1-234.567', 'birthday' => '1990-03-10',
                'personal_address' => 'Jl. Gatot Subroto No. 7, Surabaya', 'work_address' => 'Jl. HR Muhammad No. 3, Surabaya',
                'type' => 'client', 'prefix' => 'Ibu',
            ],
            [
                'name' => 'Dewi Lestari', 'organization_name' => 'PT Mandiri Abadi', 'position' => 'Marketing Staff',
                'email' => 'dewiles@mandiriabadi.com', 'phone' => '085212345678', 'whatsapp' => '085212345678',
                'npwp' => '45.678.901.2-345.678', 'birthday' => '1995-01-25',
                'personal_address' => 'Jl. Pahlawan No. 9, Yogyakarta', 'work_address' => 'Jl. Malioboro No. 18, Yogyakarta',
                'type' => 'prospect', 'prefix' => 'Ibu',
            ],
            [
                'name' => 'Eko Prasetyo', 'organization_name' => 'PT Karya Bersatu', 'position' => 'Project Manager',
                'email' => 'eko@karyabersatu.com', 'phone' => '081345678901', 'whatsapp' => '081345678901',
                'npwp' => '56.789.012.3-456.789', 'birthday' => '1982-12-05',
                'personal_address' => 'Jl. Ahmad Yani No. 33, Semarang', 'work_address' => 'Jl. Gajahmada No. 40, Semarang',
                'type' => 'partner', 'prefix' => 'Bapak',
            ],
            [
                'name' => 'Farah Sari', 'organization_name' => 'PT Digital Solusi', 'position' => 'Software Engineer',
                'email' => 'farah@digitalsolusi.com', 'phone' => '087812345678', 'whatsapp' => '087812345678',
                'npwp' => '67.890.123.4-567.890', 'birthday' => '1997-07-19',
                'personal_address' => 'Jl. Pandanaran No. 20, Semarang', 'work_address' => 'Jl. Imam Bonjol No. 15, Semarang',
                'type' => 'contractor', 'prefix' => 'Ibu',
            ],
            [
                'name' => 'Gilang Ramadhan', 'organization_name' => 'CV Multi Teknik', 'position' => 'Technical Support',
                'email' => 'gilang@multiteknik.com', 'phone' => '081276543210', 'whatsapp' => '081276543210',
                'npwp' => '78.901.234.5-678.901', 'birthday' => '1988-11-30',
                'personal_address' => 'Jl. Asia Afrika No. 55, Bandung', 'work_address' => 'Jl. Cibaduyut No. 21, Bandung',
                'type' => 'supplier', 'prefix' => 'Bapak',
            ],
            [
                'name' => 'Hana Putri', 'organization_name' => 'PT Mitra Niaga', 'position' => 'Sales Executive',
                'email' => 'hana@mitraniaga.com', 'phone' => '081923456789', 'whatsapp' => '081923456789',
                'npwp' => '89.012.345.6-789.012', 'birthday' => '1993-04-22',
                'personal_address' => 'Jl. Kaliurang No. 101, Yogyakarta', 'work_address' => 'Jl. Solo No. 33, Yogyakarta',
                'type' => 'customer', 'prefix' => 'Ibu',
            ],
            [
                'name' => 'Indra Kurniawan', 'organization_name' => 'PT Sentosa Abadi', 'position' => 'Business Development',
                'email' => 'indra@sentosaabadi.com', 'phone' => '082198765432', 'whatsapp' => '082198765432',
                'npwp' => '90.123.456.7-890.123', 'birthday' => '1986-08-11',
                'personal_address' => 'Jl. Veteran No. 9, Surabaya', 'work_address' => 'Jl. Basuki Rahmat No. 45, Surabaya',
                'type' => 'client', 'prefix' => 'Bapak',
            ],
            [
                'name' => 'Joko Widodo', 'organization_name' => 'Kemenkominfo', 'position' => 'Kepala Seksie Perangkat Lunak',
                'email' => 'joko@cahayabaru.com', 'phone' => '081377788899', 'whatsapp' => '081377788899',
                'npwp' => '91.234.567.8-901.234', 'birthday' => '1980-05-01',
                'personal_address' => 'Jl. Sudirman No. 99, Jakarta', 'work_address' => 'Jl. Thamrin No. 77, Jakarta',
                'type' => 'government', 'prefix' => 'Bapak',
            ],
        ]);
    }
}
