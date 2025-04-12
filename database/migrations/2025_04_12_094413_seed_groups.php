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
        $now = now();

        $groups = [
            ['name' => 'Gruppe 1', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gruppe 2', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gruppe 3', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gruppe 4', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gruppe 5', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gruppe 6', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('groups')->insert($groups);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('groups')->whereIn('name', [
            'Gruppe 1', 
            'Gruppe 2', 
            'Gruppe 3', 
            'Gruppe 4', 
            'Gruppe 5', 
            'Gruppe 6'
        ])->delete();
    }
};
