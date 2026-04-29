<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('password');
        });

        Schema::create('roadmap_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('area')->index();
            $table->string('status')->default('pending')->index();
            $table->string('priority')->default('medium')->index();
            $table->text('description')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->string('status')->default('open')->index();
            $table->string('priority')->default('normal')->index();
            $table->text('message');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('type')->default('info')->index();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('webhook_event_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('webhook_event_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('status')->default('open')->index();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
        });

        Schema::create('billing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('price_cents')->default(0);
            $table->string('currency', 3)->default('BRL');
            $table->unsignedInteger('event_retention_days')->default(30);
            $table->unsignedInteger('monthly_event_limit')->default(1000);
            $table->json('features')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('workspace_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('billing_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->default('mercado_pago');
            $table->string('provider_reference')->nullable()->index();
            $table->string('status')->default('trialing')->index();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_subscriptions');
        Schema::dropIfExists('billing_plans');
        Schema::dropIfExists('webhook_event_tasks');
        Schema::dropIfExists('webhook_event_notes');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('roadmap_items');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};