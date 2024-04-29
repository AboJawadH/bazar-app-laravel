<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->insert([
            'ar_name' => "تركيا",
            'en_name' => "Turkey",
            'tr_name' => "Turkey",
            'flag' => "🇹🇷",
            'phone_code' => "+90",
            'country_code' => "tr",
            'currency' => "ليرة تركية",
            'is_active' => true,
        ]);

        DB::table('countries')->insert([
            'ar_name' => "سوريا",
            'en_name' => "Syria",
            'tr_name' => "Syria",
            'flag' => "🇹🇷",
            'phone_code' => "+963",
            'country_code' => "sy",
            'currency' => "ليرة سورية",
            'is_active' => true,
        ]);
    }
}
