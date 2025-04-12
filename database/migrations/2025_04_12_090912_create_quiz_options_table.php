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
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quiz_question_id');
            $table->string('letter', 1); // A, B, C, oder D
            $table->string('option_text'); // Antworttext
            $table->boolean('is_correct')->default(false); // Kennzeichnet die richtige Antwort
            $table->timestamps();

            // Fremdschlüssel: Löschen der Optionen, wenn die zugehörige Frage gelöscht wird.
            $table->foreign('quiz_question_id')
                  ->references('id')->on('quiz_questions')
                  ->onDelete('cascade');

            // Sicherstellen, dass pro Frage jede Option (A, B, C, D) nur einmal vorkommt.
            $table->unique(['quiz_question_id', 'letter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_options');
    }
};
