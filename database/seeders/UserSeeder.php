<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            "username" => "admin",
            "email" => "admin@gmail.com",
            "first_name" => "名",
            "last_name" => "性",
            "first_name_kana" => "メイ",
            "last_name_kana" => "セイ",
            "password" => Hash::make("password"),
            "user_type" => User::USER_TYPE_ADMIN,
        ]);
        
        User::insert([
            "username" => "user1",
            "email" => "user1@gmail.com",
            "first_name" => "名",
            "last_name" => "性",
            "first_name_kana" => "メイ",
            "last_name_kana" => "セイ",
            "password" => Hash::make("password"),
            "user_type" => User::USER_TYPE_GENERAL,
        ]);
        
        User::insert([
            "username" => "user2",
            "email" => "user2@gmail.com",
            "first_name" => "名",
            "last_name" => "性",
            "first_name_kana" => "メイ",
            "last_name_kana" => "セイ",
            "password" => Hash::make("password"),
            "user_type" => User::USER_TYPE_GENERAL,
        ]);
        
        User::insert([
            "username" => "user3",
            "email" => "user3@gmail.com",
            "first_name" => "名",
            "last_name" => "性",
            "first_name_kana" => "メイ",
            "last_name_kana" => "セイ",
            "password" => Hash::make("password"),
            "user_type" => User::USER_TYPE_GENERAL,
        ]);
    }
}
