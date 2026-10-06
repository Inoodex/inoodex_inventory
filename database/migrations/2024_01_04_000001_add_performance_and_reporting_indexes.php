<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add missing performance and reporting indexes.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->index('due_payment');
            $table->index('sale_type');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->index('due');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->index('current_stock');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index('due_payment');
            $table->index('created_at');
        });

        Schema::table('project_costs', function (Blueprint $table) {
            $table->index('cost_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_costs', function (Blueprint $table) {
            $table->dropIndex(['cost_date']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['due_payment']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['current_stock']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['due']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['due_payment']);
            $table->dropIndex(['sale_type']);
        });
    }
};
