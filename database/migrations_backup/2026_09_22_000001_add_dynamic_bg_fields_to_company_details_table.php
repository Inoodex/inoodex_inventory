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
        Schema::table('company_details', function (Blueprint $table) {
            $table->string('pad_image')->nullable()->after('seal_image');
            $table->string('report_bg_image')->nullable()->after('pad_image');
            $table->boolean('show_invoice_bg')->default(true)->after('report_bg_image');
            $table->boolean('show_report_bg')->default(true)->after('show_invoice_bg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn([
                'pad_image',
                'report_bg_image',
                'show_invoice_bg',
                'show_report_bg',
            ]);
        });
    }
};
