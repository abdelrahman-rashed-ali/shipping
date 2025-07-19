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
        Schema::create('full_product_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('company_name');
            $table->string('email');
            $table->string('phone');
            $table->string('product_type')->nullable();
            $table->string('product_shape');
            $table->string('packaging_type');
            $table->string('pacage_weight');
            $table->string('quantity');
            $table->string('ship_to');
            $table->string('shipping_method');
            $table->text('additional_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('full_product_requests');
    }
};
