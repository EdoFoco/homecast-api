<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class PropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('properties')->insert([
            "user_id" => 1,
            "description" => "Very nice house.",
            "address" => "Oxford St, London, UK",
            "postcode" => "W1",
            "latitude" => 51.51495659999999,
            "longitude" => -0.1445616,
            "google_place_id" => "ChIJwTXDVNUadkgRGnzGNLlkIdo",
            "thumbnail" => "https://i.ytimg.com/vi/Mi1owViNFXM/maxresdefault.jpg",
            "price" => "100000.00",
            "bedrooms" => 7,
            "bathrooms" => 5,
            "living_rooms" => 3,
            "listing_active" => true
       ]);

        DB::table('properties')->insert([
            "user_id" => 1,
            "description" => "Very nice house.",
            "address" => "55 Blue Anchor Ln, London SE16 3TS, UK",
            "postcode" => "SE16 3TS",
            "google_place_id" => "EiY1NSBCbHVlIEFuY2hvciBMbiwgTG9uZG9uIFNFMTYgM1RTLCBVSw",
            "latitude" => 51.492531,
            "longitude" => -0.0628745,
            "thumbnail" => "http://www.app-tischner.com/wp-content/uploads/2017/08/03_opportune_full.jpg",
            "price" => "1800.00",
            "bedrooms" => 2,
            "bathrooms" => 1,
            "living_rooms" => 1,
            "listing_active" => true
       ]);

       DB::table('properties')->insert([
            "user_id" => 1,
            "description" => "Very nice house.",
            "address" => "Columbia Rd, London E2 7RN, UK",
            "google_place_id" => "ChIJF7iYlbgcdkgRoitSmu5Ch8g",
            "postcode" => "E2 7RN",
            "latitude" => 51.528436,
            "longitude" =>  -0.0714422,
            "thumbnail" => "https://s-ec.bstatic.com/images/hotel/max1024x768/782/78239966.jpg",
            "price" => "3000.00",
            "bedrooms" => 5,
            "bathrooms" => 3,
            "living_rooms" => 2,
            "listing_active" => true
       ]);
    }
}
