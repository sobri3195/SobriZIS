<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mustahik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('asnaf', [
                'fakir',
                'miskin',
                'amil',
                'muallaf',
                'riqab',
                'gharimin',
                'fisabilillah',
                'ibnu_sabil'
            ]);
            $table->string('nik_masked', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->text('description')->nullable();
            $table->json('verification_docs')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'inactive'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->decimal('total_received', 15, 2)->default(0);
            $table->integer('distribution_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['asnaf', 'status']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mustahik');
    }
};
