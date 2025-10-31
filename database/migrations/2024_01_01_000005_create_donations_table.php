<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 50)->unique();
            $table->foreignId('donor_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('donor_phone', 20)->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('unique_code', 8, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('admin_fee', 10, 2)->default(0);
            $table->enum('payment_method', [
                'qris',
                'va_bca',
                'va_bni',
                'va_bri',
                'va_mandiri',
                'va_permata',
                'gopay',
                'ovo',
                'dana',
                'linkaja',
                'shopeepay',
                'credit_card',
                'bank_transfer',
                'other'
            ])->nullable();
            $table->enum('payment_gateway', ['midtrans', 'xendit', 'doku', 'manual'])->default('midtrans');
            $table->string('gateway_transaction_id')->nullable();
            $table->text('gateway_response')->nullable();
            $table->enum('status', [
                'pending',
                'waiting_payment',
                'processing',
                'success',
                'failed',
                'expired',
                'cancelled',
                'refunded'
            ])->default('pending');
            $table->boolean('is_anonymous')->default(false);
            $table->text('message')->nullable();
            $table->string('receipt_number', 50)->nullable()->unique();
            $table->string('receipt_pdf')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'payment_method', 'payment_gateway']);
            $table->index(['donor_id', 'program_id']);
            $table->index('order_id');
            $table->index('paid_at');
        });

        Schema::create('donation_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['email', 'whatsapp', 'sms'])->default('email');
            $table->enum('event', ['pending', 'success', 'failed', 'reminder'])->default('pending');
            $table->text('content')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_notifications');
        Schema::dropIfExists('donations');
    }
};
