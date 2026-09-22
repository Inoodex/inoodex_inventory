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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->enum('status', ['0','1'])->default(1);
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('district_id');
            $table->string('name', 255);
            $table->enum('status', ['0','1'])->default(1);
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->string('name', 255);
            $table->string('email', 255)->nullable();
            $table->string('phone', 255);
            $table->string('alternative_phone', 255)->nullable();
            $table->bigInteger('state');
            $table->bigInteger('area');
            $table->text('address_details');
            $table->text('comment')->nullable();
            $table->enum('address_type', ['1','2']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('districts');
    }
};
