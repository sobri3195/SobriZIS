<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->string('payout_number', 50)->unique();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('mustahik_id')->constrained('mustahik')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'e_wallet', 'other'])->default('cash');
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->json('proof_files')->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'disbursed', 'rejected'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('disbursed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disbursed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'program_id']);
            $table->index('mustahik_id');
        });

        Schema::create('payout_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['approved', 'rejected'])->default('approved');
            $table->text('notes')->nullable();
            $table->integer('level')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_approvals');
        Schema::dropIfExists('payouts');
    }
};
