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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organization_name');
            $table->string('position');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('npwp')->nullable();
            $table->date('birthday')->nullable();
            $table->text('personal_address')->nullable();
            $table->text('work_address')->nullable();
            $table->enum('type', [
                'supplier',             // pemasok
                'customer',             // pelanggan
                'client',               // pelanggan setia
                'prospect',             // calon pelanggan (CRM)
                'partner',              // rekan bisnis
                'contractor',           // karyawan eksternal / kontraktor
                'government',           // pemerintah
                'other',                // pihak ketiga lain-lain
            ])->index();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
