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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Optionaler kurzer Titel
            $table->text('question_text'); // Die Frage oder Aufgabenbeschreibung
            $table->enum('type', ['quiz', 'story'])->default('quiz'); // Unterscheidung, falls du Quiz- vs. Storyfragen hast
            $table->integer('order')->default(0); // Optional: Reihenfolge, wenn mehrere Fragen angezeigt werden sollen
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
