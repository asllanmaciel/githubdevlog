<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('mercado_pago')->index();
            $table->string('provider_event_id');
            $table->string('request_id')->nullable()->index();
            $table->string('event_type')->index();
            $table->string('action')->nullable()->index();
            $table->string('resource_id')->nullable()->index();
            $table->string('external_reference')->nullable()->index();
            $table->string('status')->default('received')->index();
            $table->boolean('signature_valid')->default(false);
            $table->json('payload')->nullable();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('workspace_subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('billing_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_events');
    }
};
