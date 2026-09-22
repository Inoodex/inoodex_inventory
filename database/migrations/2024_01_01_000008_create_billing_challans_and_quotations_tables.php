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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 255)->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('client_id');
            $table->string('client_name', 255)->nullable();
            $table->string('client_designation', 255)->nullable();
            $table->text('client_address')->nullable();
            $table->string('client_phone', 255)->nullable();
            $table->string('client_email', 255)->nullable();
            $table->string('attention_to', 255)->nullable();
            $table->text('body_content')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->string('subject', 255)->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('signatory_name', 255)->nullable();
            $table->string('signatory_designation', 255)->nullable();
            $table->string('company_phone', 255)->nullable();
            $table->string('company_email', 255)->nullable();
            $table->string('company_website', 255)->nullable();
            $table->text('additional_enclosed')->nullable();
            $table->date('quotation_date');
            $table->date('expiry_date');
            $table->text('notes')->nullable();
            $table->decimal('sub_total', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->boolean('show_signature')->default(true);
            $table->boolean('show_seal')->default(true);
            $table->enum('status', ['draft','sent','accepted','rejected','expired'])->default('draft');
            $table->index('customer_id');
            $table->index('client_id');
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('product_id');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->index('quotation_id');
            $table->index('product_id');
            $table->timestamps();
        });

        Schema::create('challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_number', 255)->unique();
            $table->string('reference_number', 255);
            $table->date('challan_date');
            $table->enum('type', ['sale','project']);
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('recipient_organization', 255)->nullable();
            $table->string('recipient_designation', 255)->nullable();
            $table->text('recipient_address')->nullable();
            $table->string('attention_to', 255)->nullable();
            $table->string('subject', 255)->nullable();
            $table->boolean('show_signature')->default(true);
            $table->boolean('show_seal')->default(true);
            $table->text('notes')->nullable();
            $table->string('company_name', 255)->nullable();
            $table->string('signatory_name', 255)->nullable();
            $table->string('signatory_designation', 255)->nullable();
            $table->string('company_phone', 255)->nullable();
            $table->string('company_email', 255)->nullable();
            $table->string('company_website', 255)->nullable();
            $table->string('designation', 255)->nullable();
            $table->index('sale_id');
            $table->index('project_id');
            $table->timestamps();
        });

        Schema::create('challan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challan_id');
            $table->text('description');
            $table->integer('quantity')->default(1);
            $table->string('unit', 255)->default('Piece');
            $table->index('challan_id');
            $table->timestamps();
        });

        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number', 255)->unique();
            $table->string('reference_number', 255)->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->enum('type', ['sale','project']);
            $table->string('work_order_number', 255)->nullable();
            $table->date('bill_date');
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->boolean('show_signature')->default(true);
            $table->boolean('show_seal')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('bank_detail_id')->nullable();
            $table->unsignedBigInteger('company_detail_id')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->string('subject', 255)->nullable();
            $table->string('attention_to', 255)->nullable();
            $table->string('designation', 255)->nullable();
            $table->index('sale_id');
            $table->index('project_id');
            $table->index('customer_id');
            $table->index('client_id');
            $table->index('bank_detail_id');
            $table->index('company_detail_id');
            $table->timestamps();
        });

        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->string('description', 255);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->index('bill_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('challan_items');
        Schema::dropIfExists('challans');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
