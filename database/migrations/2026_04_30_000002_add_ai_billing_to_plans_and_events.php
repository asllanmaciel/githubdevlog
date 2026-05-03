<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_plans', function (Blueprint $table) {
            $table->unsignedInteger('monthly_ai_analysis_limit')->default(0)->after('monthly_event_limit');
            $table->unsignedInteger('ai_analysis_overage_price_cents')->default(0)->after('monthly_ai_analysis_limit');
        });

        Schema::table('webhook_events', function (Blueprint $table) {
            $table->string('ai_analysis_type')->nullable()->after('ai_provider');
            $table->unsignedInteger('ai_estimated_cost_cents')->default(0)->after('ai_analysis_type');
            $table->unsignedInteger('ai_input_tokens')->nullable()->after('ai_estimated_cost_cents');
            $table->unsignedInteger('ai_output_tokens')->nullable()->after('ai_input_tokens');
            $table->text('ai_error')->nullable()->after('ai_output_tokens');
        });
    }

    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropColumn([
                'ai_analysis_type',
                'ai_estimated_cost_cents',
                'ai_input_tokens',
                'ai_output_tokens',
                'ai_error',
            ]);
        });

        Schema::table('billing_plans', function (Blueprint $table) {
            $table->dropColumn([
                'monthly_ai_analysis_limit',
                'ai_analysis_overage_price_cents',
            ]);
        });
    }
};
