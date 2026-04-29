<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('webhook_secret');
            $table->string('github_app_installation_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('workspace_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->timestamps();
            $table->unique(['workspace_id', 'user_id']);
        });

        Schema::create('github_installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('installation_id')->index();
            $table->string('account_login');
            $table->string('account_type')->nullable();
            $table->json('permissions')->nullable();
            $table->json('events')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('github_id')->nullable()->index();
            $table->string('full_name')->index();
            $table->boolean('private')->default(false);
            $table->string('default_branch')->nullable();
            $table->timestamps();
        });

        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('repository_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source')->default('github')->index();
            $table->string('event_name')->index();
            $table->string('action')->nullable()->index();
            $table->string('delivery_id')->nullable()->index();
            $table->boolean('signature_valid')->default(false);
            $table->string('validation_method')->nullable();
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('received_at')->index();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('repositories');
        Schema::dropIfExists('github_installations');
        Schema::dropIfExists('workspace_members');
        Schema::dropIfExists('workspaces');
    }
};
