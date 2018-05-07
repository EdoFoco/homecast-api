<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReadAtToConvsationUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mc_conversation_user', function (Blueprint $table) {
            $table->boolean('wasRead')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mc_conversation_user', function (Blueprint $table) {
            $table->dropColumn('wasRead');
        });
    }
}
