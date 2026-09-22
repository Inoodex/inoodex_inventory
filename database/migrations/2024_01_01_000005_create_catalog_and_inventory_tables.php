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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->boolean('status')->default(true);
            $table->integer('order_by')->default(0);
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name', 255);
            $table->unsignedBigInteger('brand_id');
            $table->string('model', 255);
            $table->string('barcode', 255)->nullable()->unique();
            $table->json('photos')->nullable();
            $table->enum('status', ['0','1'])->default(1);
            $table->integer('warranty')->default(0);
            $table->boolean('is_serialized')->default(false);
            $table->index('brand_id');
            $table->index('category_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_serials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('sales_item_id')->nullable();
            $table->string('serial_number', 255)->unique();
            $table->enum('status', ['available','sold','damaged','returned'])->default('available');
            $table->index('product_id');
            $table->index('purchase_id');
            $table->index('sales_item_id');
            $table->timestamps();
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->integer('opening_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->text('notes')->nullable();
            $table->index('product_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('product_serials');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
