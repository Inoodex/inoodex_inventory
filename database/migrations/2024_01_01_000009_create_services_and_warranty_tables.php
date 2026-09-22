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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->string('name', 255);
            $table->string('country_code', 255);
            $table->string('phone', 255);
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('product_name', 255);
            $table->string('product_number', 255)->nullable();
            $table->text('details')->nullable();
            $table->enum('status', ['0','1'])->default(1);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->string('name', 255);
            $table->string('country_code', 255)->nullable();
            $table->string('phone', 255);
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name', 255);
            $table->string('product_number', 255)->nullable();
            $table->text('details')->nullable();
            $table->double('total');
            $table->double('bill');
            $table->double('paid_amount')->default(0);
            $table->double('discount')->nullable();
            $table->double('due_amount');
            $table->text('remarks')->nullable();
            $table->integer('warranty_duration')->nullable();
            $table->bigInteger('repaired_by')->nullable();
            $table->enum('status', ['0','1'])->default(1);
            $table->date('complated_date')->nullable();
            $table->index('product_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('created_at');
            $table->timestamps();
        });

        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_no', 255)->unique();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('sales_item_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('serial_number', 255)->nullable();
            $table->date('claim_date');
            $table->date('warranty_expiry_date');
            $table->text('problem_description');
            $table->text('condition_notes')->nullable();
            $table->enum('status', ['pending','under_inspection','sent_to_vendor','repaired','replaced','rejected','completed'])->default('pending');
            $table->enum('action_taken', ['none','repair','replacement','refund'])->default('none');
            $table->string('replacement_serial_number', 255)->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->index('sales_item_id');
            $table->index('product_id');
            $table->index('customer_id');
            $table->index('received_by');
            $table->index(['claim_no', 'status']);
            $table->index(['sale_id', 'product_id']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('warranty_claim_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warranty_claim_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status', 255);
            $table->text('note')->nullable();
            $table->index('warranty_claim_id');
            $table->index('user_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_claim_logs');
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('services');
        Schema::dropIfExists('bookings');
    }
};
