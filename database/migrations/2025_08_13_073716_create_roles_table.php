<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // Akses
            $table->boolean('akses_daftar_jabatan')->default(1);
            $table->boolean('akses_tambah_jabatan')->default(1);
            $table->boolean('akses_edit_jabatan')->default(1);
            $table->boolean('akses_hapus_jabatan')->default(1);
            $table->boolean('akses_daftar_user')->default(1);
            $table->boolean('akses_tambah_user')->default(1);
            $table->boolean('akses_edit_user')->default(1);
            $table->boolean('akses_hapus_user')->default(1);
            $table->boolean('akses_daftar_akun')->default(1);
            $table->boolean('akses_tambah_akun')->default(1);
            $table->boolean('akses_edit_akun')->default(1);
            $table->boolean('akses_hapus_akun')->default(1);
            $table->boolean('akses_daftar_jurnal')->default(1);
            $table->boolean('akses_tambah_jurnal')->default(1);
            // $table->boolean('akses_edit_jurnal')->default(1);
            $table->boolean('akses_hapus_jurnal')->default(1);
            $table->boolean('akses_daftar_gudang')->default(1);
            $table->boolean('akses_tambah_gudang')->default(1);
            $table->boolean('akses_edit_gudang')->default(1);
            $table->boolean('akses_hapus_gudang')->default(1);
            $table->boolean('akses_daftar_satuan')->default(1);
            $table->boolean('akses_tambah_satuan')->default(1);
            $table->boolean('akses_edit_satuan')->default(1);
            $table->boolean('akses_hapus_satuan')->default(1);
            $table->boolean('akses_daftar_kategori_barang')->default(1);
            $table->boolean('akses_tambah_kategori_barang')->default(1);
            $table->boolean('akses_edit_kategori_barang')->default(1);
            $table->boolean('akses_hapus_kategori_barang')->default(1);
            $table->boolean('akses_daftar_barang')->default(1);
            $table->boolean('akses_tambah_barang')->default(1);
            $table->boolean('akses_edit_barang')->default(1);
            $table->boolean('akses_hapus_barang')->default(1);
            $table->boolean('akses_daftar_transaksi_barang')->default(1);
            $table->boolean('akses_tambah_transaksi_barang')->default(1);
            $table->boolean('akses_edit_transaksi_barang')->default(1);
            $table->boolean('akses_hapus_transaksi_barang')->default(1);
            $table->boolean('akses_edit_setting')->default(1);
            $table->boolean('akses_daftar_kontak')->default(1);
            $table->boolean('akses_tambah_kontak')->default(1);
            $table->boolean('akses_edit_kontak')->default(1);
            $table->boolean('akses_hapus_kontak')->default(1);
            // $table->boolean('akses_daftar_pesanan')->default(1);
            // $table->boolean('akses_tambah_pesanan')->default(1);
            // $table->boolean('akses_edit_pesanan')->default(1);
            // $table->boolean('akses_hapus_pesanan')->default(1);
            $table->boolean('akses_daftar_pembelian')->default(1);
            $table->boolean('akses_tambah_pembelian')->default(1);
            $table->boolean('akses_edit_pembelian')->default(1);
            $table->boolean('akses_hapus_pembelian')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
