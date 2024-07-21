<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('meal_plans')->insert([
            'name' => 'Bed & Breakfast',
            'price' => 1000,
        ]);

        DB::table('meal_plans')->insert([
            'name' => 'Half Board',
            'price' => 2000,
        ]);

        DB::table('meal_plans')->insert([
            'name' => 'Full Board',
            'price' => 3000,
        ]);
    }
}
