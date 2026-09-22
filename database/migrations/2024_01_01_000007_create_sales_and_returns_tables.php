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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 255)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('sale_type', 255)->default('retail');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->double('qty');
            $table->double('total');
            $table->decimal('vat', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('delivery_charge', 10, 2)->default(0.00);
            $table->double('payble');
            $table->double('bill');
            $table->decimal('advanced_payment', 15, 2)->nullable();
            $table->decimal('due_payment', 15, 2)->nullable();
            $table->double('discount')->nullable();
            $table->string('sales_by', 255)->nullable();
            $table->enum('status', ['paid','partial','credit'])->default('credit');
            $table->index('product_id');
            $table->index('project_id');
            $table->index('client_id');
            $table->index('customer_id');
            $table->index('sales_by');
            $table->index('created_at');
            $table->index('status');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->double('unit_price');
            $table->integer('warranty')->default(0);
            $table->integer('qty');
            $table->double('total_price');
            $table->integer('returned_qty')->default(0);
            $table->timestamps();
        });

        Schema::create('daily_sales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description')->nullable();
            $table->decimal('card_amount', 8, 2)->nullable();
            $table->decimal('cash_amount', 8, 2)->nullable();
            $table->decimal('others_amount', 8, 2)->nullable();
            $table->decimal('total_amount', 8, 2)->nullable();
            $table->text('assigned_person_id');
            $table->enum('status', ['0','1'])->default(1);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_for');
            $table->bigInteger('customer_id');
            $table->bigInteger('sale_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->enum('payment_method', ['cash','card','bank_transfer'])->default('cash');
            $table->double('amount');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->enum('status', ['0','1'])->default(1);
            $table->index('created_by');
            $table->index('updated_by');
            $table->timestamps();
        });

        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('customer_id');
            $table->date('return_date');
            $table->decimal('total_refund_amount', 12, 2)->default(0.00);
            $table->enum('status', ['pending','approved','completed','rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->index('sale_id');
            $table->index('customer_id');
            $table->index('status');
            $table->timestamps();
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sales_item_id')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->enum('return_reason', ['damaged','wrong_item','customer_changed_mind','defective','expired','other'])->default('other');
            $table->enum('condition', ['good','damaged','defective'])->default('good');
            $table->text('notes')->nullable();
            $table->index('return_id');
            $table->index('product_id');
            $table->index('sales_item_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('daily_sales');
        Schema::dropIfExists('sales_items');
        Schema::dropIfExists('sales');
    }
};
