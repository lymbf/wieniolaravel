<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Uruchom migrację.
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('answer_text');
            $table->timestamp('answered_at')->nullable(); // Data i godzina odpowiedzi
            $table->timestamps();
        });
    }

    /**
     * Cofnij migrację.
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
