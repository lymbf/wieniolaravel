<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Uruchom migrację.
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');         // Ścieżka do pliku
            $table->string('original_name');     // Oryginalna nazwa pliku
            // Polimorficzne powiązanie z pytaniami lub odpowiedziami
            $table->unsignedBigInteger('attachable_id');
            $table->string('attachable_type');
            $table->timestamps();
        });
    }

    /**
     * Cofnij migrację.
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
