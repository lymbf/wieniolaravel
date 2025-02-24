<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingCommentAttachmentsTable extends Migration
{
    /**
     * Uruchom migrację.
     */
    public function up()
    {
        Schema::create('meeting_comment_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_comment_id');
            $table->string('file_path');
            $table->string('original_name');
            $table->timestamps();

            $table->foreign('meeting_comment_id')->references('id')->on('meeting_comments')->onDelete('cascade');
        });
    }

    /**
     * Cofnij migrację.
     */
    public function down()
    {
        Schema::dropIfExists('meeting_comment_attachments');
    }
}

