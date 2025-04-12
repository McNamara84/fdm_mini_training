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
                'option_text' => 'Wettervorhersagen',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 2
        $q2Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '2. Welche Eigenschaften sollten Forschungsdaten im Sinne der FAIR-Prinzipien besitzen?',
            'sort_order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q2Id,
                'letter' => 'A',
                'option_text' => 'Findbar, zugänglich, interoperabel und wiederverwendbar',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q2Id,
                'letter' => 'B',
                'option_text' => 'Schnell, billig, schön und perfekt',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q2Id,
                'letter' => 'C',
                'option_text' => 'Privat, geheim, isoliert und unzugänglich',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q2Id,
                'letter' => 'D',
                'option_text' => 'Zufällig, unstrukturiert, uneinheitlich und frei interpretierbar',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 3
        $q3Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '3. Welche Rolle spielen Metadaten im Forschungsdatenmanagement?',
            'sort_order' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q3Id,
                'letter' => 'A',
                'option_text' => 'Sie beschreiben die Daten und ermöglichen so eine leichtere Auffindbarkeit und Interpretation',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q3Id,
                'letter' => 'B',
                'option_text' => 'Sie speichern direkt alle Rohdaten',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q3Id,
                'letter' => 'C',
                'option_text' => 'Sie dokumentieren ausschließlich experimentelle Fehler',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q3Id,
                'letter' => 'D',
                'option_text' => 'Sie ersetzen die Originaldaten',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 4
        $q4Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '4. Wie kann die Qualität von Forschungsdaten sichergestellt werden?',
            'sort_order' => 4,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q4Id,
                'letter' => 'A',
                'option_text' => 'Durch standardisierte Verfahren zur Datenerhebung, Validierung und Dokumentation',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q4Id,
                'letter' => 'B',
                'option_text' => 'Durch spontane Dateneingabe ohne Überprüfung',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q4Id,
                'letter' => 'C',
                'option_text' => 'Durch manuelle Speicherung in unstrukturierten Dateien',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q4Id,
                'letter' => 'D',
                'option_text' => 'Durch ausschließliche Nutzung analoger Methoden',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 5 (Geowissenschaftliches Beispiel)
        $q5Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '5. Ein Bohrkern aus einem geologischen Untersuchungsgebiet zeigt verschiedene Gesteinsschichten. Welche Information ist am wichtigsten für die Interpretation der Daten?',
            'sort_order' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q5Id,
                'letter' => 'A',
                'option_text' => 'Die stratigraphische Reihenfolge der Gesteinsschichten',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q5Id,
                'letter' => 'B',
                'option_text' => 'Die Farbe des Bohrkerns',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q5Id,
                'letter' => 'C',
                'option_text' => 'Die Temperatur im Bohrloch',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q5Id,
                'letter' => 'D',
                'option_text' => 'Die Verpackung der Probensammlung',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 6
        $q6Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '6. Welches Ziel verfolgt ein effektives Forschungsdatenmanagement an Hochschulen?',
            'sort_order' => 6,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q6Id,
                'letter' => 'A',
                'option_text' => 'Die nachhaltige Verfügbarkeit, Transparenz und Wiederverwendbarkeit von Forschungsdaten',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q6Id,
                'letter' => 'B',
                'option_text' => 'Die monetäre Verwertung von Daten durch Verkauf an Dritte',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q6Id,
                'letter' => 'C',
                'option_text' => 'Die komplette Archivierung ohne praktische Nutzbarkeit',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q6Id,
                'letter' => 'D',
                'option_text' => 'Die Verbreitung von Daten ausschließlich innerhalb kleiner Forscherteams',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Frage 7 (Schwer)
        $q7Id = DB::table('quiz_questions')->insertGetId([
            'question_text' => '7. Welche technischen und organisatorischen Maßnahmen sind zentral, um die Integrität und Authentizität von Forschungsdaten zu gewährleisten?',
            'sort_order' => 7,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('quiz_options')->insert([
            [
                'quiz_question_id' => $q7Id,
                'letter' => 'A',
                'option_text' => 'Einsatz von digitalen Signaturen, detaillierter Protokollierung und strikter Zugangskontrolle',
                'is_correct' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q7Id,
                'letter' => 'B',
                'option_text' => 'Tägliche manuelle Überprüfung durch mehrere Personen',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q7Id,
                'letter' => 'C',
                'option_text' => 'Speicherung der Daten auf ungeschützten Servern',
                'is_correct' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'quiz_question_id' => $q7Id,
                'letter' => 'D',
                'option_text' => 'Verwendung von unverschlüsselten E-Mail-Anhängen zum Datenaustausch',
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
