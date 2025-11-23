<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Pakaian Pria', 'slug' => 'pakaian-pria'],
            ['name' => 'Pakaian Wanita', 'slug' => 'pakaian-wanita'],
            ['name' => 'Elektronik', 'slug' => 'elektronik'],
            ['name' => 'Sepatu', 'slug' => 'sepatu'],
            ['name' => 'Tas', 'slug' => 'tas'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}
