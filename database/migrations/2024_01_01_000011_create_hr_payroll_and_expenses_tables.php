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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('employee_id', 255)->unique();
            $table->string('name', 255);
            $table->string('email', 255)->nullable()->unique();
            $table->string('phone', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('designation', 255)->nullable();
            $table->date('join_date')->nullable();
            $table->decimal('salary', 12, 2)->default(0.00);
            $table->string('status', 255)->default('active');
            $table->index('user_id');
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('attendance_status')->default(1);
            $table->enum('status', ['0','1'])->default(1);
            $table->text('remarks')->nullable();
            $table->index('date');
            $table->timestamps();
        });

        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->date('date');
            $table->double('amount');
            $table->enum('status', ['0','1'])->default(1);
            $table->text('remarks')->nullable();
            $table->index('date');
            $table->timestamps();
        });

        Schema::create('advance_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->date('request_date');
            $table->text('notes')->nullable();
            $table->index('employee_id');
            $table->timestamps();
        });

        Schema::create('ta_das', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->decimal('used_amount', 10, 2)->default(0.00);
            $table->decimal('remaining_amount', 10, 2)->default(0.00);
            $table->text('purpose')->nullable();
            $table->enum('type', ['TA','DA'])->default('TA');
            $table->enum('payment_type', ['Advance','Claim'])->default('Advance');
            $table->index('employee_id');
            $table->index('user_id');
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('daily_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->unsignedBigInteger('expense_category_id');
            $table->decimal('amount', 15, 2);
            $table->enum('spend_method', ['cash','card','bank_transfer']);
            $table->text('remarks')->nullable();
            $table->index('user_id');
            $table->index('employee_id');
            $table->index('date');
            $table->index('expense_category_id');
            $table->timestamps();
        });

        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->decimal('total_sales', 15, 2)->default(0.00);
            $table->decimal('total_purchases', 15, 2)->default(0.00);
            $table->decimal('total_expenses', 15, 2)->default(0.00);
            $table->decimal('net_profit', 15, 2)->nullable();
            $table->string('remarks', 255)->nullable();
            $table->unique(['year', 'month']);
            $table->index('year');
            $table->index('month');
            $table->timestamps();
        });

        Schema::create('extras', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->decimal('value', 8, 2)->nullable();
            $table->enum('status', ['0','1'])->default(1);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extras');
        Schema::dropIfExists('revenues');
        Schema::dropIfExists('daily_expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('ta_das');
        Schema::dropIfExists('advance_salaries');
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('employees');
    }
};
