<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->string('status')->default('accepted')->index()->after('signature_valid');
            $table->text('failure_reason')->nullable()->after('status');
            $table->unsignedInteger('retry_count')->default(0)->after('failure_reason');
            $table->timestamp('last_retried_at')->nullable()->after('retry_count');
            $table->timestamp('reprocessed_at')->nullable()->after('last_retried_at');
            $table->string('dedupe_key')->nullable()->index()->after('delivery_id');
        });
    }

    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['dedupe_key']);
            $table->dropColumn([
                'status',
                'failure_reason',
                'retry_count',
                'last_retried_at',
                'reprocessed_at',
                'dedupe_key',
            ]);
        });
    }
};
