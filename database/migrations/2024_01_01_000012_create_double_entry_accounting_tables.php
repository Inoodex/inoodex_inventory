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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 50)->unique();
            $table->string('account_name', 255);
            $table->enum('account_type', ['asset','liability','equity','revenue','expense']);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedInteger('level')->default(1);
            $table->unsignedBigInteger('bank_detail_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->index('bank_detail_id');
            $table->index(['account_type', 'is_active']);
            $table->index('parent_id');
            $table->timestamps();
        });

        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->string('year_name', 50)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->index('closed_by');
            $table->index(['is_active', 'is_closed']);
            $table->index(['start_date', 'end_date']);
            $table->timestamps();
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('journal_no', 50)->unique();
            $table->date('entry_date');
            $table->unsignedBigInteger('fiscal_year_id')->nullable();
            $table->string('reference_type', 50)->default('manual');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('total_debit', 15, 2)->default(0.00);
            $table->decimal('total_credit', 15, 2)->default(0.00);
            $table->enum('status', ['draft','posted','approved','reversed'])->default('posted');
            $table->unsignedBigInteger('reversed_entry_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->index('reversed_entry_id');
            $table->index('created_by');
            $table->index('approved_by');
            $table->index(['entry_date', 'status']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('fiscal_year_id');
            $table->timestamps();
        });

        Schema::create('journal_entry_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->index(['account_id', 'journal_entry_id']);
            $table->index('journal_entry_id');
            $table->timestamps();
        });

        Schema::create('contra_entries', function (Blueprint $table) {
            $table->id();
            $table->string('contra_no', 50)->unique();
            $table->unsignedBigInteger('from_account_id');
            $table->unsignedBigInteger('to_account_id');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->index('from_account_id');
            $table->index('to_account_id');
            $table->index('journal_entry_id');
            $table->index('created_by');
            $table->index(['date', 'from_account_id', 'to_account_id']);
            $table->timestamps();
        });

        Schema::create('account_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->date('bank_statement_date');
            $table->decimal('statement_balance', 15, 2)->default(0.00);
            $table->decimal('book_balance', 15, 2)->default(0.00);
            $table->decimal('difference', 15, 2)->default(0.00);
            $table->enum('status', ['draft','completed'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->index('created_by');
            $table->index(['account_id', 'bank_statement_date']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_reconciliations');
        Schema::dropIfExists('contra_entries');
        Schema::dropIfExists('journal_entry_items');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('fiscal_years');
        Schema::dropIfExists('chart_of_accounts');
    }
};
