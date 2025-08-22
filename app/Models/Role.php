<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function semua_akses()
    {
        $data = [
            'akses_daftar_jabatan' => 'Akses Daftar Jabatan',
            'akses_tambah_jabatan' => 'Akses Tambah Jabatan',
            'akses_edit_jabatan' => 'Akses Edit Jabatan',
            'akses_hapus_jabatan' => 'Akses Hapus Jabatan',
            'akses_daftar_user' => 'Akses Daftar User',
            'akses_tambah_user' => 'Akses Tambah User',
            'akses_edit_user' => 'Akses Edit User',
            'akses_hapus_user' => 'Akses Hapus User',
            'akses_daftar_akun' => 'Akses Daftar Akun',
            'akses_tambah_akun' => 'Akses Tambah Akun',
            'akses_edit_akun' => 'Akses Edit Akun',
            'akses_hapus_akun' => 'Akses Hapus Akun',
            'akses_daftar_jurnal' => 'Akses Daftar Jurnal',
            'akses_tambah_jurnal' => 'Akses Tambah Jurnal',
            // 'akses_edit_jurnal' => 'Akses Edit Jurnal',
            'akses_hapus_jurnal' => 'Akses Hapus Jurnal',
            'akses_daftar_gudang' => 'Akses Daftar Gudang',
            'akses_tambah_gudang' => 'Akses Tambah Gudang',
            'akses_edit_gudang' => 'Akses Edit Gudang',
            'akses_hapus_gudang' => 'Akses Hapus Gudang',
            'akses_daftar_satuan' => 'Akses Daftar Satuan',
            'akses_tambah_satuan' => 'Akses Tambah Satuan',
            'akses_edit_satuan' => 'Akses Edit Satuan',
            'akses_hapus_satuan' => 'Akses Hapus Satuan',
            'akses_daftar_kategori_barang' => 'Akses Daftar Kategori Barang',
            'akses_tambah_kategori_barang' => 'Akses Tambah Kategori Barang',
            'akses_edit_kategori_barang' => 'Akses Edit Kategori Barang',
            'akses_hapus_kategori_barang' => 'Akses Hapus Kategori Barang',
            'akses_daftar_barang' => 'Akses Daftar Barang',
            'akses_tambah_barang' => 'Akses Tambah Barang',
            'akses_edit_barang' => 'Akses Edit Barang',
            'akses_hapus_barang' => 'Akses Hapus Barang',
            'akses_daftar_transaksi_barang' => 'Akses Daftar Transaksi Barang',
            'akses_tambah_transaksi_barang' => 'Akses Tambah Transaksi Barang',
            'akses_edit_transaksi_barang' => 'Akses Edit Transaksi Barang',
            'akses_hapus_transaksi_barang' => 'Akses Hapus Transaksi Barang',
            'akses_edit_setting' => 'Akses Edit Setting',
        ];
        return $data;
    }

    public static function semua_akses2()
    {
        $data = [
            'akses_daftar_jabatan',
            'akses_tambah_jabatan',
            'akses_edit_jabatan',
            'akses_hapus_jabatan',
            'akses_daftar_user',
            'akses_tambah_user',
            'akses_edit_user',
            'akses_hapus_user',
            'akses_daftar_akun',
            'akses_tambah_akun',
            'akses_edit_akun',
            'akses_hapus_akun',
            'akses_daftar_jurnal',
            'akses_tambah_jurnal',
            'akses_hapus_jurnal',
            'akses_daftar_gudang',
            'akses_tambah_gudang',
            'akses_edit_gudang',
            'akses_hapus_gudang',
            'akses_daftar_satuan',
            'akses_tambah_satuan',
            'akses_edit_satuan',
            'akses_hapus_satuan',
            'akses_daftar_kategori_barang',
            'akses_tambah_kategori_barang',
            'akses_edit_kategori_barang',
            'akses_hapus_kategori_barang',
            'akses_daftar_barang',
            'akses_tambah_barang',
            'akses_edit_barang',
            'akses_hapus_barang',
            'akses_daftar_transaksi_barang',
            'akses_tambah_transaksi_barang',
            'akses_edit_transaksi_barang',
            'akses_hapus_transaksi_barang',
            'akses_edit_setting',
        ];
        return $data;
    }
}
