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
        $data = [
            [
                'name' => 'Category 1',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Category::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
