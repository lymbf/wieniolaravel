<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->text('notatka')->nullable()->after('original_name');
        });
    }
    
    public function down()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn('notatka');
        });
    }
    
};
