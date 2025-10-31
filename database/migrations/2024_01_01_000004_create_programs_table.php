<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->enum('category', [
                'zakat_maal',
                'zakat_profesi',
                'zakat_pertanian',
                'infaq',
                'sedekah',
                'wakaf',
                'fidyah',
                'other'
            ])->default('infaq');
            $table->enum('asnaf', [
                'fakir',
                'miskin',
                'amil',
                'muallaf',
                'riqab',
                'gharimin',
                'fisabilillah',
                'ibnu_sabil',
                'all'
            ])->default('all');
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->decimal('collected_amount', 15, 2)->default(0);
            $table->decimal('distributed_amount', 15, 2)->default(0);
            $table->integer('total_donors')->default(0);
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('region')->nullable();
            $table->json('meta')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'category', 'asnaf']);
            $table->index('tenant_id');
            $table->fullText(['title', 'description']);
        });

        Schema::create('program_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->json('images')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_updates');
        Schema::dropIfExists('programs');
    }
};
