<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            'name' => 'Office',
        ]);

        DB::table('departments')->insert([
            'name' => 'Maintenance',
        ]);

        DB::table('departments')->insert([
            'name' => 'House Keeping',
        ]);

        DB::table('departments')->insert([
            'name' => 'Restaurant',
        ]);

        DB::table('departments')->insert([
            'name' => 'Bar',
        ]);

        DB::table('departments')->insert([
            'name' => 'Kitchen',
        ]);
    }
}
