<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Non-destructive: adds missing payroll breakdown fields to salaries table.
     */
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('salaries', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('salaries', 'month')) {
                $table->string('month', 20)->nullable()->after('employee_id')->index();
            }
            if (!Schema::hasColumn('salaries', 'basic_salary')) {
                $table->decimal('basic_salary', 12, 2)->default(0.00)->after('month');
            }
            if (!Schema::hasColumn('salaries', 'advance')) {
                $table->decimal('advance', 12, 2)->default(0.00)->after('basic_salary');
            }
            if (!Schema::hasColumn('salaries', 'allowance')) {
                $table->decimal('allowance', 12, 2)->default(0.00)->after('advance');
            }
            if (!Schema::hasColumn('salaries', 'deduction')) {
                $table->decimal('deduction', 12, 2)->default(0.00)->after('allowance');
            }
            if (!Schema::hasColumn('salaries', 'net_salary')) {
                $table->decimal('net_salary', 12, 2)->default(0.00)->after('deduction');
            }
            if (!Schema::hasColumn('salaries', 'payment_status')) {
                $table->string('payment_status', 20)->default('paid')->after('net_salary')->index();
            }
            if (!Schema::hasColumn('salaries', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('salaries', 'note')) {
                $table->text('note')->nullable()->after('payment_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $columns = ['employee_id', 'month', 'basic_salary', 'advance', 'allowance', 'deduction', 'net_salary', 'payment_status', 'payment_date', 'note'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('salaries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
