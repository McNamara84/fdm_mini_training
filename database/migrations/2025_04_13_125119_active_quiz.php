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
        Schema::create('active_quiz', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_question_id');
            $table->timestamps();
        });

        // Setze initial den aktiven Fragensatz auf die erste Frage (sortiert nach sort_order)
        $firstQuestionId = DB::table('quiz_questions')->orderBy('sort_order')->value('id');
        DB::table('active_quiz')->insert([
            'quiz_question_id' => $firstQuestionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_quiz');
    }
};
