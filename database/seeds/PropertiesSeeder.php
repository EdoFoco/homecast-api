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
            "name" => "Chambord Mansion",
            "address" => "Oxford St, London, UK",
            "latitude" => 51.51495659999999,
            "longitude" => -0.1445616,
            "google_place_id" => "ChIJwTXDVNUadkgRGnzGNLlkIdo",
            "thumbnail" => "https://i.ytimg.com/vi/Mi1owViNFXM/maxresdefault.jpg",
            "price" => "100000.00",
            "bedrooms" => 7,
            "bathrooms" => 5,
            "living_rooms" => 3,
            "type" => "rent",
            "minimum_rental_period" => 12,
            "listing_active" => true
       ]);

       DB::table('description_sections')->insert([
                "property_id" => 1,
                "title" => "Key Features",
                "description" => "- Bright living room\n- Close to station\n- Spacious bedrooms"
            ]);
    
       DB::table('description_sections')->insert([
                "property_id" => 1,
                "title" => "Description",
                "description" => "Three story house in the heart of London. Situated 5minutes walking from Oxford Street it boosts 5 double bedrooms, three bathrooms, two balconies, an enormous kitchen and an amazing terrace. This place is perfect for parties of all kind.. Three story house in the heart of London"
        ]);

        DB::table('properties')->insert([
            "user_id" => 1,
            "name" => "Casa di Groot",
            "address" => "55 Blue Anchor Ln, London SE16 3TS, UK",
            "google_place_id" => "EiY1NSBCbHVlIEFuY2hvciBMbiwgTG9uZG9uIFNFMTYgM1RTLCBVSw",
            "latitude" => 51.492531,
            "longitude" => -0.0628745,
            "thumbnail" => "http://www.app-tischner.com/wp-content/uploads/2017/08/03_opportune_full.jpg",
            "price" => "1800.00",
            "bedrooms" => 2,
            "bathrooms" => 1,
            "living_rooms" => 1,
            "type" => "buy",
            "minimum_rental_period" => null,
            "listing_active" => true
       ]);

       DB::table('description_sections')->insert([
                "property_id" => 2,
                "title" => "Key Features",
                "description" => "- Bright living room\n- Close to station\n- Spacious bedrooms"
            ]);

        DB::table('description_sections')->insert([
                "property_id" => 2,
                "title" => "Description",
                "description" => "Three story house in the heart of London. Situated 5minutes walking from Oxford Street it boosts 5 double bedrooms, three bathrooms, two balconies, an enormous kitchen and an amazing terrace. This place is perfect for parties of all kind.. Three story house in the heart of London"
        ]);

        DB::table('properties')->insert([
            "user_id" => 1,
            "name" => "Whitfield",
            "address" => "Columbia Rd, London E2 7RN, UK",
            "google_place_id" => "ChIJF7iYlbgcdkgRoitSmu5Ch8g",
            "latitude" => 51.528436,
            "longitude" =>  -0.0714422,
            "thumbnail" => "https://s-ec.bstatic.com/images/hotel/max1024x768/782/78239966.jpg",
            "price" => "3000.00",
            "bedrooms" => 5,
            "bathrooms" => 3,
            "living_rooms" => 2,
            "type" => "rent",
            "minimum_rental_period" => 3,
            "listing_active" => true
       ]);

       DB::table('description_sections')->insert([
            "property_id" => 3,
            "title" => "Key Features",
            "description" => "- Bright living room\n- Close to station\n- Spacious bedrooms"
        ]);

        DB::table('description_sections')->insert([
            "property_id" => 3,
            "title" => "Description",
            "description" => "Three story house in the heart of London. Situated 5minutes walking from Oxford Street it boosts 5 double bedrooms, three bathrooms, two balconies, an enormous kitchen and an amazing terrace. This place is perfect for parties of all kind.. Three story house in the heart of London"
        ]);
    }
}
