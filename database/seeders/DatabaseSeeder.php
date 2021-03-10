<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application"s database.
     *
     * @return void
     */
    public function run()
    {
         $userType = ["customer","service_agent","service_admin"];

         for($i = 1; $i <= 6; $i++) {
             User::create([
                 "name" => "testUser$i",
                 "email" => "testUser$i@gmail.com",
                 "user_type" => $userType[mt_rand (0,2)],
                 "password" => Hash::make("123456")
             ]);
         }
    }
}
