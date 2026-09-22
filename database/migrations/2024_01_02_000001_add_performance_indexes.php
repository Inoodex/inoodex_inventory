<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add missing performance indexes.
     */
    public function up(): void
    {
        Schema::table('sales_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('sale_id');
            $table->index('project_id');
            $table->index('payment_method');
            $table->index('created_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('phone');
            $table->index('status');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->index('phone');
            $table->index('status');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->index('phone');
            $table->index('email');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('phone');
            $table->index('status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['user_id', 'date']);
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->index('user_id');
            $table->index(['user_id', 'date']);
        });

        Schema::table('advance_salaries', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('client_id');
            $table->index('challan_date');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->index('quotation_date');
            $table->index('status');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->index('bill_date');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->index('phone');
            $table->index('complated_date');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['group']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['complated_date']);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropIndex(['bill_date']);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropIndex(['quotation_date']);
            $table->dropIndex(['status']);
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['challan_date']);
        });

        Schema::table('advance_salaries', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['user_id', 'date']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['user_id', 'date']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['status']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['email']);
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['status']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['sale_id']);
            $table->dropIndex(['project_id']);
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('sales_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });
    }
};
