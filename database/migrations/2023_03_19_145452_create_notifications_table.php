<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type');
            $table->string('message');
            $table->foreignId('project_id')->nullable(true)->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable(true)->constrained('users')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable(true)->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('assigner_id')->nullable(true)->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
