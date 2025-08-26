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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('date')->index();
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('contact_id')->constrained();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('freight', 15, 2)->default(0);
            $table->decimal('expense', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0)->index();
            $table->decimal('paid', 15, 2)->default(0)->index();
            $table->decimal('remaining', 15, 2)->default(0)->index();
            $table->string('currency')->default('IDR');
            $table->enum('status', [
                'Menunggu Pembayaran',
                'Belum Tuntas',
                'Lunas',
            ])->default('Menunggu Pembayaran')->index();
            $table->foreignId('user_id')->constrained();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
