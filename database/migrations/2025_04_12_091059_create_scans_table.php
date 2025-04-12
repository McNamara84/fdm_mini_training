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
        Schema::create('scans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quiz_question_id'); // Referenz zur Frage
            $table->unsignedBigInteger('qr_code_id');         // Referenz zum gescannten QR-Code
            $table->unsignedBigInteger('group_id');           // Redundante Speicherung der Gruppe (zur Vereinfachung)
            $table->timestamp('scanned_at')->useCurrent();    // Zeitpunkt des Scans
            $table->timestamps();

            // Fremdschlüssel
            $table->foreign('quiz_question_id')
                  ->references('id')->on('quiz_questions')
                  ->onDelete('cascade');

            $table->foreign('qr_code_id')
                  ->references('id')->on('qr_codes')
                  ->onDelete('cascade');

            $table->foreign('group_id')
                  ->references('id')->on('groups')
                  ->onDelete('cascade');

            // Jede Gruppe darf pro Frage nur einmal antworten.
            $table->unique(['quiz_question_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
