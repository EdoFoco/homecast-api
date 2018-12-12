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
            $table->longText('description')->nullable();
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('google_place_id');
            $table->float('latitude', 10, 6)->nullable();
            $table->float('longitude', 10, 6)->nullable();
            $table->string('thumbnail')->nullable();
            $table->decimal('price', 9, 2)->nullable();;
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();;
            $table->integer('living_rooms')->nullable();;
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
