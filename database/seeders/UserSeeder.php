<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => "عبد الرحمن",
            'email' => Str::random(10).'@example.com',
            'phone_number' => Str::random(10),
            'is_blocked' => true,
            'password' => Hash::make('password'),
        ]);

        DB::table('users')->insert([
            'name' => "هشام",
            'email' => Str::random(10).'@example.com',
            'phone_number' => Str::random(10),
            'is_blocked' => true,
            'password' => Hash::make('password'),
        ]);
    }
}
