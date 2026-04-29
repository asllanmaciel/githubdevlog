<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_usage_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('billing_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('period', 7)->index();
            $table->unsignedInteger('events_count')->default(0);
            $table->unsignedInteger('monthly_limit')->default(0);
            $table->unsignedInteger('usage_percent')->default(0);
            $table->unsignedInteger('overage_count')->default(0);
            $table->timestamp('period_started_at');
            $table->timestamp('period_ended_at');
            $table->timestamp('captured_at');
            $table->timestamps();

            $table->unique(['workspace_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_usage_snapshots');
    }
};