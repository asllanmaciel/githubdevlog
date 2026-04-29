<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->string('category')->default('technical')->after('priority')->index();
            $table->timestamp('first_response_due_at')->nullable()->after('message');
            $table->timestamp('resolution_due_at')->nullable()->after('first_response_due_at');
            $table->timestamp('responded_at')->nullable()->after('resolution_due_at');
            $table->text('internal_notes')->nullable()->after('responded_at');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'first_response_due_at',
                'resolution_due_at',
                'responded_at',
                'internal_notes',
            ]);
        });
    }
};
