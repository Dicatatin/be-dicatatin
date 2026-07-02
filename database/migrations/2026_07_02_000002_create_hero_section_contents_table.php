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
        Schema::create('hero_section_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title_prefix', 255);
            $table->string('title_highlight', 255);
            $table->string('title_suffix', 255);
            $table->text('subtitle');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_section_contents');
    }
};
