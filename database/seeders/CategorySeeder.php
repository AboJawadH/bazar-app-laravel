<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Subcategory;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category =  Category::create([
            'ar_name' => "احذية",
            'en_name' => "shoes",
            'tr_name' => "shoes",
            'parent_section_id' => "1",
            'parent_section_name' => "القسم العام",
            'order_number' => 1,
            'is_active' => true,
        ]);

        Subcategory::create([
            'ar_name' => "نايك",
            'en_name' => "nayk",
            'tr_name' => "nayk",
            'parent_section_id' => "1",
            'parent_section_name' => "القسم العام",
            'category_id' => $category->id,
            'category_name' => $category->ar_name,
            'order_number' => 1,
            'is_active' => true,
        ]);
    }
}
