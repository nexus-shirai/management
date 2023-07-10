<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                "category_name" => "システム"
            ],
            [
                "category_name" => "デザイン"
            ],
            [
                "category_name" => "コンテンツ"
            ],
            [
                "category_name" => "コーディング"
            ],
        ];

        foreach ($categories as $category) {
            Category::insert([
                "category_name" => $category["category_name"]
            ]);
        }
    }
}
