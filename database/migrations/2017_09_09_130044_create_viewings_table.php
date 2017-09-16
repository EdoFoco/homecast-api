<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viewings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('property_id')->unsigned();
            $table->timestamp('date_time');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')
            ->on('users')
            ->onDelete('cascade');

            $table->foreign('property_id')->references('id')
            ->on('properties')
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
        Schema::drop('viewings');
    }
}
