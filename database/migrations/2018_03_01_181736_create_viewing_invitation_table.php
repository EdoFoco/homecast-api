<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewingInvitationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viewing_invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('viewing_id')->unsigned();
            $table->integer('user_id')->nullable();
            $table->string('user_email')->nullable();
            $table->timestamps();
            
            $table->foreign('viewing_id')->references('id')
            ->on('viewings')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('viewing_invitations');
    }
}
