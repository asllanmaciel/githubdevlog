<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bug_reports', function (Blueprint $table) {
            $table->id();
            $table->string('fingerprint', 64)->unique();
            $table->string('level')->default('error')->index();
            $table->string('exception_class');
            $table->text('message');
            $table->string('file')->nullable();
            $table->unsignedInteger('line')->nullable();
            $table->string('method', 12)->nullable();
            $table->text('url')->nullable();
            $table->string('route')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_hash', 64)->nullable();
            $table->unsignedInteger('occurrences')->default(1);
            $table->json('context')->nullable();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamp('resolved_at')->nullable()->index();
            $table->timestamps();

            $table->index(['level', 'resolved_at']);
            $table->index(['exception_class', 'last_seen_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_reports');
    }
};
