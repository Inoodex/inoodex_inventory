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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name', 255);
            $table->unsignedBigInteger('client_id');
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('sub_total', 12, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2);
            $table->decimal('advanced_payment', 12, 2)->default(0.00);
            $table->decimal('due_payment', 12, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->enum('status', ['pending','in_progress','completed','cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->index('client_id');
            $table->index('status');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->index('project_id');
            $table->index('product_id');
            $table->timestamps();
        });

        Schema::create('cost_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('project_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('cost_category_id');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('cost_date');
            $table->index('project_id');
            $table->index('cost_category_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_costs');
        Schema::dropIfExists('cost_categories');
        Schema::dropIfExists('project_items');
        Schema::dropIfExists('projects');
    }
};
