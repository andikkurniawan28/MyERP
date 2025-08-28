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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_account_id')->constrained('accounts');
            $table->foreignId('stock_in_account_id')->constrained('accounts');
            $table->foreignId('stock_out_account_id')->constrained('accounts');

            $table->foreignId('purchase_subtotal_account_id')->constrained('accounts');
            $table->foreignId('purchase_discount_account_id')->constrained('accounts');
            $table->foreignId('purchase_tax_account_id')->constrained('accounts');
            $table->foreignId('purchase_freight_account_id')->constrained('accounts');
            $table->foreignId('purchase_expenses_account_id')->constrained('accounts');
            $table->foreignId('purchase_grand_total_account_id')->constrained('accounts');

            $table->foreignId('sales_subtotal_account_id')->constrained('accounts');
            $table->foreignId('sales_discount_account_id')->constrained('accounts');
            $table->foreignId('sales_tax_account_id')->constrained('accounts');
            $table->foreignId('sales_freight_account_id')->constrained('accounts');
            $table->foreignId('sales_expenses_account_id')->constrained('accounts');
            $table->foreignId('sales_grand_total_account_id')->constrained('accounts');
            $table->foreignId('sales_cogs_account_id')->constrained('accounts');

            $table->foreignId('retained_earning_account_id')->constrained('accounts');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
