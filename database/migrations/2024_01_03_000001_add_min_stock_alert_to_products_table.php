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
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'min_stock_alert')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('min_stock_alert')->default(5)->after('is_serialized');
                $table->index('min_stock_alert');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'min_stock_alert')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['min_stock_alert']);
                $table->dropColumn('min_stock_alert');
            });
        }
    }
};
