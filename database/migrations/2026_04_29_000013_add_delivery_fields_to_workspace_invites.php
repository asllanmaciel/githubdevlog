<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspace_invites', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('expires_at');
            $table->text('delivery_error')->nullable()->after('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('workspace_invites', function (Blueprint $table) {
            $table->dropColumn(['sent_at', 'delivery_error']);
        });
    }
};
