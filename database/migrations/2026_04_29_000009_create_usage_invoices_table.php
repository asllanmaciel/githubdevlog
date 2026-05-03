<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('billing_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('workspace_usage_snapshot_id')->nullable()->constrained()->nullOnDelete();
            $table->string('period', 7)->index();
            $table->string('status')->default('draft')->index();
            $table->unsignedInteger('events_count')->default(0);
            $table->unsignedInteger('monthly_limit')->default(0);
            $table->unsignedInteger('overage_count')->default(0);
            $table->unsignedInteger('overage_price_cents')->default(0);
            $table->unsignedInteger('amount_cents')->default(0);
            $table->string('currency', 3)->default('BRL');
            $table->string('provider')->nullable();
            $table->string('provider_reference')->nullable()->index();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['workspace_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_invoices');
    }
};
