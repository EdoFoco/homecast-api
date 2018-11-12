<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
           'email' => 'test@test.com',
           'password' => bcrypt('test'),
           'name' => 'Alice',
           'profile_picture' => 'https://pbs.twimg.com/profile_images/810944438193233920/pWtfOp4R.jpg',
       ]);
    }
}
