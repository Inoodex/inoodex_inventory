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
        Schema::create('company_details', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('signatory_name', 255);
            $table->string('signatory_designation', 255);
            $table->string('signature_image', 255)->nullable();
            $table->string('seal_image', 255)->nullable();
            $table->string('pad_image', 255)->nullable();
            $table->string('report_bg_image', 255)->nullable();
            $table->boolean('show_invoice_bg')->default(true);
            $table->boolean('show_report_bg')->default(true);
            $table->string('phone', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->string('account_name', 255);
            $table->string('bank_name', 255);
            $table->string('branch', 255);
            $table->string('account_number', 255);
            $table->string('account_type', 255);
            $table->string('routing_number', 255)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_details');
        Schema::dropIfExists('company_details');
    }
};
