<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            // CASA
            ['space_type' => 'CASA', 'name' => 'Mercado', 'icon' => null],
            ['space_type' => 'CASA', 'name' => 'Energia', 'icon' => null],
            ['space_type' => 'CASA', 'name' => 'Água', 'icon' => null],
            ['space_type' => 'CASA', 'name' => 'Internet', 'icon' => null],
            ['space_type' => 'CASA', 'name' => 'Gás', 'icon' => null],
            ['space_type' => 'CASA', 'name' => 'Farmácia', 'icon' => null],
            ['space_type' => 'CASA', 'name' => 'IPTU', 'icon' => null],
            ['space_type' => 'CASA', 'name' => 'Seguro', 'icon' => null],
            ['space_type' => 'CASA', 'name' => 'Financiamento', 'icon' => null],

            // EVENTO
            ['space_type' => 'EVENTO', 'name' => 'Carne', 'icon' => null],
            ['space_type' => 'EVENTO', 'name' => 'Bebidas', 'icon' => null],
            ['space_type' => 'EVENTO', 'name' => 'Transporte', 'icon' => null],
            ['space_type' => 'EVENTO', 'name' => 'Hospedagem', 'icon' => null],
            ['space_type' => 'EVENTO', 'name' => 'Aluguel', 'icon' => null],
            ['space_type' => 'EVENTO', 'name' => 'Presentes', 'icon' => null],

            // Global
            ['space_type' => null, 'name' => 'Outros', 'icon' => null],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['space_type' => $category['space_type'], 'name' => $category['name']],
                $category
            );
        }
    }
}
