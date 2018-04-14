<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewingReservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viewing_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('viewing_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')
            ->on('users')
            ->onDelete('cascade');
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
        Schema::drop('viewing_reservations');
    }
}
