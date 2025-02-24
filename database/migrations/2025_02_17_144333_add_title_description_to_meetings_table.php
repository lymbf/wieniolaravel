<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleDescriptionToMeetingsTable extends Migration
{
    /**
     * Uruchom migrację.
     */
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('title')->after('user_id');
            $table->text('description')->after('title');
        });
    }

    /**
     * Cofnij migrację.
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }
}
