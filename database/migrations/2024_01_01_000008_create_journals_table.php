<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense'])->default('asset');
            $table->enum('category', [
                'kas',
                'bank',
                'piutang',
                'zakat_maal',
                'zakat_profesi',
                'infaq',
                'sedekah',
                'wakaf',
                'amil',
                'distribution',
                'operational',
                'other'
            ])->default('other');
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'category']);
        });

        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('journal_number', 50)->unique();
            $table->date('transaction_date');
            $table->enum('type', ['debit', 'credit'])->default('debit');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_reconciled')->default(false);
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('budget_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->decimal('allocated_amount', 15, 2);
            $table->decimal('used_amount', 15, 2)->default(0);
            $table->integer('fiscal_year');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_allocations');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('accounts');
    }
};
