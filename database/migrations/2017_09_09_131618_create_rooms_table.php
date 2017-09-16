<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::create('rooms', function (Blueprint $table) {
             $table->increments('id');
             $table->integer('viewing_id')->unsigned();
             $table->string('unique_id');
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
        Schema::drop('rooms');
    }
}
