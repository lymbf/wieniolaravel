<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Uruchom migrację.
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['in_progress', 'resolved'])->default('in_progress');
            $table->timestamp('asked_at')->nullable(); // Data i godzina zadania pytania
            $table->timestamps();
        });
    }

    /**
     * Cofnij migrację.
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
