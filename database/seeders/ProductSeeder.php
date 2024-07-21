<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            'id' => 1,
            'category_id' => 1,
            'code' => '101',
            'name' => 'Swimming',
            'description' => 'Swimming',
            'price' => '500',
            'buying_price' => 0,
            'taxable' => 1
        ]);

    }
}
