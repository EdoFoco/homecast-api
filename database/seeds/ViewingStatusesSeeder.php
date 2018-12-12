<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ViewingStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('viewing_statuses')->insert([
           'status' => 'ACTIVE'
       ]);

       DB::table('viewing_statuses')->insert([
        'status' => 'CANCELLED'
       ]);

       DB::table('viewing_statuses')->insert([
        'status' => 'LIVE'
       ]);

       DB::table('viewing_statuses')->insert([
        'status' => 'WAITING_FOR_PRESENTER'
       ]);
    }
}
