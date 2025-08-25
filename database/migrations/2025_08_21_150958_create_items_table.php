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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_category_id')->constrained();
            $table->string('barcode')->nullable()->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description');
            $table->foreignId('main_unit_id')->constrained('units');
            // $table->foreignId('secondary_unit_id')->constrained('units');
            // $table->double('conversion_rate');
            // $table->decimal('purchase_price_secondary', 15, 2);
            // $table->decimal('selling_price_secondary', 15, 2)->nullable();
            $table->decimal('purchase_price_main', 15, 2);
            $table->decimal('selling_price_main', 15, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
