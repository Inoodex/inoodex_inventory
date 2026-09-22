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
            $table->string('purchase_no', 50)->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('vendor_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('sub_price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('payment', 10, 2)->nullable();
            $table->decimal('due', 10, 2)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('product_id');
            $table->index('vendor_id');
            $table->index('created_at');
            $table->index('purchase_no');
            $table->timestamps();
            $table->softDeletes();
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
