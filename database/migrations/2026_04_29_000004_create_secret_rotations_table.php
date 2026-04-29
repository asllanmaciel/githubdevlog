<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->timestamp('webhook_secret_rotated_at')->nullable()->after('webhook_secret');
        });

        Schema::create('secret_rotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('secret_type')->index();
            $table->string('rotated_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('rotated_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secret_rotations');

        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn('webhook_secret_rotated_at');
        });
    }
};
