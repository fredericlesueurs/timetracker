<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('estimated_duration')->nullable();
            $table->enum('status', ['WAITING', 'IN_PROGRESS', 'TERMINATED', 'PAID'])->default('WAITING');
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('duration')->nullable();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
