<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->text('ai_summary')->nullable()->after('payload');
            $table->string('ai_risk_level')->nullable()->after('ai_summary');
            $table->json('ai_action_items')->nullable()->after('ai_risk_level');
            $table->json('ai_signals')->nullable()->after('ai_action_items');
            $table->string('ai_provider')->nullable()->after('ai_signals');
            $table->timestamp('ai_generated_at')->nullable()->after('ai_provider');
        });
    }

    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropColumn([
                'ai_summary',
                'ai_risk_level',
                'ai_action_items',
                'ai_signals',
                'ai_provider',
                'ai_generated_at',
            ]);
        });
    }
};
