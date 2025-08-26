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
        Schema::create('sales_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_id')->constrained()->onDelete('cascade');
            $table->decimal('total', 15, 2)->default(0);
            // $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_payment_details');
    }
};
