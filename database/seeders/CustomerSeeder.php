<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts')->insert([
            'name' => 'Walk in',
            'type' => 2,
            'email' => 'n/a',
            'telephone' => 'n/a',
            'location' => 'n/a',
            'vat_registration_number' => 'n/a',
        ]);
    }
}
