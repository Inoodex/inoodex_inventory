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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('country_code', 255)->nullable();
            $table->string('phone', 255);
            $table->string('email', 255)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('address')->nullable();
            $table->string('images', 255)->nullable();
            $table->integer('verification_code')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->bigInteger('billing_address')->nullable();
            $table->bigInteger('shipping_address')->nullable();
            $table->enum('status', ['0','1'])->default(1);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('phone', 20);
            $table->string('email', 255)->nullable();
            $table->text('address');
            $table->enum('status', ['0','1'])->default(1);
            $table->timestamps();
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('phone', 255);
            $table->string('email', 255);
            $table->text('address');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('customers');
    }
};
