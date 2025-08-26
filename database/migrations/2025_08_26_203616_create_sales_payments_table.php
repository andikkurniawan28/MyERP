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
        Schema::create('sales_payments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('date')->index();
            $table->decimal('grand_total', 15, 2)->default(0)->index(); // total pembayaran
            $table->string('currency')->default('IDR');
            // $table->enum('status', ['pending', 'paid'])->default('pending')->index();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('contact_id')->constrained();
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
        Schema::dropIfExists('sales_payments');
    }
};
