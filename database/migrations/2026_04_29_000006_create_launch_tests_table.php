<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('launch_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('area')->index();
            $table->string('priority')->default('medium')->index();
            $table->string('status')->default('pending')->index();
            $table->text('instructions')->nullable();
            $table->text('expected_result')->nullable();
            $table->text('evidence')->nullable();
            $table->string('executed_by')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('launch_tests');
    }
};
