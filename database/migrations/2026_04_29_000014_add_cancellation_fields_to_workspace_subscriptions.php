<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspace_subscriptions', function (Blueprint $table) {
            $table->timestamp('canceled_at')->nullable()->after('current_period_ends_at');
            $table->string('cancel_reason')->nullable()->after('canceled_at');
            $table->json('lifecycle_metadata')->nullable()->after('cancel_reason');
        });
    }

    public function down(): void
    {
        Schema::table('workspace_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['canceled_at', 'cancel_reason', 'lifecycle_metadata']);
        });
    }
};