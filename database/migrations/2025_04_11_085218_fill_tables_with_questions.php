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
        // Aktueller Zeitstempel
        $now = now();

        // 1. Quizfrage: "Was sind Forschungsdaten?"
        $q1Id = DB::table('questions')->insertGetId([
            'title'         => 'Was sind Forschungsdaten?',
            'question_text' => 'Was sind Forschungsdaten?',
            'type'          => 'quiz',
            'order'         => 1,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        DB::table('question_options')->insert([
            [
                'question_id'   => $q1Id,
                'option_letter' => 'A',
                'option_text'   => 'Nur Excel-Dateien',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'question_id'   => $q1Id,
                'option_letter' => 'B',
                'option_text'   => 'Alle in der Forschung erzeugten oder genutzten Daten',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'question_id'   => $q1Id,
                'option_letter' => 'C',
                'option_text'   => 'Nur veröffentlichte Ergebnisse',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'question_id'   => $q1Id,
                'option_letter' => 'D',
                'option_text'   => 'Nur digitale Daten',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ]);

        // 2. Quizfrage: "Warum sollte man Forschungsdaten gut dokumentieren?"
        $q2Id = DB::table('questions')->insertGetId([
            'title'         => 'Warum Daten dokumentieren?',
            'question_text' => 'Warum sollte man Forschungsdaten gut dokumentieren?',
            'type'          => 'quiz',
            'order'         => 2,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        DB::table('question_options')->insert([
            [
                'question_id'   => $q2Id,
                'option_letter' => 'A',
                'option_text'   => 'Weil es ordentlich aussieht',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'question_id'   => $q2Id,
                'option_letter' => 'B',
                'option_text'   => 'Weil es Pflicht ist',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'question_id'   => $q2Id,
                'option_letter' => 'C',
                'option_text'   => 'Um Daten nachvollziehbar und wiederverwendbar zu machen',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'question_id'   => $q2Id,
                'option_letter' => 'D',
                'option_text'   => 'Weil sonst die Datei verloren geht',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ]);

        // 3. Story-Frage: Interaktive Story
        $q3Id = DB::table('questions')->insertGetId([
            'title'         => 'Interaktive Story',
            'question_text' => 'Dein Kollege braucht dringend Zugriff auf deine Daten, aber die Dokumentation fehlt. Was tust du?',
            'type'          => 'story',
            'order'         => 3,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        DB::table('question_options')->insert([
            [
                'question_id'   => $q3Id,
                'option_letter' => 'A',
                'option_text'   => 'Schnell improvisieren',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'question_id'   => $q3Id,
                'option_letter' => 'B',
                'option_text'   => 'Sofort die Dokumentation nachholen',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'question_id'   => $q3Id,
                'option_letter' => 'C',
                'option_text'   => 'Den Kollegen informieren, dass die Daten noch nicht bereit sind',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'question_id'   => $q3Id,
                'option_letter' => 'D',
                'option_text'   => 'Auf einen USB-Stick ausweichen',
                'token'         => Str::random(10),
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('question_options')->truncate();
        DB::table('questions')->truncate();
    }
};
