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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('group_id');
            $table->string('letter', 1); // A, B, C oder D
            $table->timestamps();

            // Fremdschlüssel: Löschen der QR-Codes, wenn die zugehörige Gruppe gelöscht wird.
            $table->foreign('group_id')
                  ->references('id')->on('groups')
                  ->onDelete('cascade');

            // Jede Gruppe darf pro Buchstabe nur einen QR-Code haben.
            $table->unique(['group_id', 'letter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
