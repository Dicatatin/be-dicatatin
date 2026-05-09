<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('Untitled');
            $table->string('method');
            $table->enum('ai_status', ['processing', 'completed', 'failed'])->default('processing');
            $table->string('image_url')->nullable();
            $table->text('clean_text')->nullable();

            // JSONB untuk performa query dan fleksibilitas struktur FE
            $table->jsonb('nodes')->nullable();
            $table->jsonb('edges')->nullable();
            $table->jsonb('flashcards')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
