<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();

        // Frage 1 (Einsteigerfrage)
        $q1Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '1. Was sind Forschungsdaten?',
            'sort_order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q1Id,
                'letter' => 'A',
                'option_text' => 'Rohdaten, Messwerte und Beobachtungen aus wissenschaftlichen Untersuchungen',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q1Id,
                'letter' => 'B',
                'option_text' => 'Buchhaltungsdaten von Unternehmen',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q1Id,
                'letter' => 'C',
                'option_text' => 'Persönliche Daten von Studierenden',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q1Id,
                'letter' => 'D',
                'option_text' => 'Daten, die die EU als Forschungsdaten festgelegt hat',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 2
        $q2Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '2. Welche Herausforderung besteht bei der Definition von Forschungsdaten?',
            'sort_order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q2Id,
                'letter' => 'A',
                'option_text' => 'Weil die NFDI festlegt was Forschungsdaten sind, ist keine besondere Definition notwendig',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q2Id,
                'letter' => 'B',
                'option_text' => 'Das Verständnis variiert stark zwischen Fachbereichen und Projekten',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q2Id,
                'letter' => 'C',
                'option_text' => 'Forschungsdaten sind immer standardisiert, was die Definition vereinfacht',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q2Id,
                'letter' => 'D',
                'option_text' => 'Nur große Forschungsprojekte benötigen eine Definition von Forschungsdaten',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 3
        $q3Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '3. Warum ist es wichtig, auch Software und Simulationen als Forschungsdaten anzusehen?',
            'sort_order' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q3Id,
                'letter' => 'A',
                'option_text' => 'Weil sie meist kostenlos verfügbar sind',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q3Id,
                'letter' => 'B',
                'option_text' => 'Weil sie leicht reproduzierbar sind',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q3Id,
                'letter' => 'C',
                'option_text' => 'Weil sie keine menschliche Interpretation erfordern',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q3Id,
                'letter' => 'D',
                'option_text' => 'Weil sie oft die Grundlage für Schlussfolgerungen und Erkenntnisse bilden',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 4
        $q4Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '4. Welchen Zweck verfolgt das Forschungsdatenmanagement primär?',
            'sort_order' => 4,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q4Id,
                'letter' => 'A',
                'option_text' => 'Die reine Datensammlung zu beschleunigen',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q4Id,
                'letter' => 'B',
                'option_text' => 'Die Qualitätssicherung der Daten über den gesamten Forschungsprozess',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q4Id,
                'letter' => 'C',
                'option_text' => 'Die Datenmenge zu maximieren, um umfassendere Analysen zu ermöglichen',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q4Id,
                'letter' => 'D',
                'option_text' => 'Die Datenspeicherung zu vereinfachen, unabhängig von der Qualität',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 5 (Geowissenschaftliches Beispiel)
        $q5Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '5. Wie trägt Forschungsdatenmanagement zur Transparenz von Forschung bei',
            'sort_order' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q5Id,
                'letter' => 'A',
                'option_text' => 'Durch die Verschlüsselung sensibler Daten',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q5Id,
                'letter' => 'B',
                'option_text' => 'Durch die detaillierte Dokumentation und Nachvollziehbarkeit der Forschungsprozesse',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q5Id,
                'letter' => 'C',
                'option_text' => 'Durch die Beschränkung des Zugangs zu den Daten auf bestimmte Personen',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q5Id,
                'letter' => 'D',
                'option_text' => 'Durch die Nutzung proprietärer Software',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 6
        $q6Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '6. Welchen Vorteil bietet die Nachnutzung von Forschungsdaten?',
            'sort_order' => 6,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q6Id,
                'letter' => 'A',
                'option_text' => 'Sie vermeidet unnötige Doppelarbeit und spart Ressourcen',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q6Id,
                'letter' => 'B',
                'option_text' => 'Sie erhöht den Wettbewerb zwischen Forschungsgruppen',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q6Id,
                'letter' => 'C',
                'option_text' => 'Sie führt zu einer geringeren Publikationsrate',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q6Id,
                'letter' => 'D',
                'option_text' => 'Sie ist irrelevant für die wissenschaftliche Gemeinschaft',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 7 (Schwer)
        $q7Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '7. Wie wirkt sich das Teilen von Forschungsdaten auf die Sichtbarkeit der Forschenden aus?',
            'sort_order' => 7,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q7Id,
                'letter' => 'A',
                'option_text' => 'Es hat keinen Einfluss',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q7Id,
                'letter' => 'B',
                'option_text' => 'Es kann die Sichtbarkeit und Zitationsrate erhöhen',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q7Id,
                'letter' => 'C',
                'option_text' => 'Es führt zu Datenschutzproblemen',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q7Id,
                'letter' => 'D',
                'option_text' => 'Es reduziert die Notwendigkeit für eigene Publikationen',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Lösche zunächst die Antwortmöglichkeiten und anschließend die Fragen anhand der sort_order-Spalte
        DB::table('quiz_options')->whereIn('quiz_question_id', function ($query) {
            $query->select('id')->from('quiz_questions')->whereIn('sort_order', [1,2,3,4,5,6,7]);
        })->delete();

        DB::table('quiz_questions')->whereIn('sort_order', [1,2,3,4,5,6,7])->delete();
    }
};
