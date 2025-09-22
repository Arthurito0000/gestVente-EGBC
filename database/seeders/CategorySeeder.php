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
            [
                'code' => 'CAT-001',
                'name' => 'Électronique',
                'description' => 'Appareils et composants électroniques'
            ],
            [
                'code' => 'CAT-002',
                'name' => 'Alimentaire',
                'description' => 'Produits alimentaires et boissons'
            ],
            [
                'code' => 'CAT-003',
                'name' => 'Vêtements',
                'description' => 'Vêtements et accessoires'
            ],
            [
                'code' => 'CAT-004',
                'name' => 'Autre',
                'description' => 'Autres produits divers'
            ]
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
