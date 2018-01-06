<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->string('address');
            $table->string('postcode');
            $table->string('city');
            $table->string('thumbnail');
            $table->decimal('price', 9, 2);
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('living_rooms');
            $table->string('type');
            $table->integer('minimum_rental_period')->nullable();
            $table->boolean('listing_active')->default(false);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')
            ->on('users')
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
        Schema::drop('properties');
    }
}
