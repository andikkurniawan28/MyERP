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
        ];
        return $data;
    }
}
