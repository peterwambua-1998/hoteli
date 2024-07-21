<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageFacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('package_facilities')->insert([
            'name' => 'Accommodation'
        ]);

        DB::table('package_facilities')->insert([
            'name' => 'Conference Hall'
        ]);

        DB::table('package_facilities')->insert([
            'name' => 'Swimming'
        ]);

        DB::table('package_facilities')->insert([
            'name' => 'Zip lining'
        ]);

        DB::table('package_facilities')->insert([
            'name' => 'breakfast'
        ]);

        DB::table('package_facilities')->insert([
            'name' => "10 O'Clock Tea"
        ]);

        DB::table('package_facilities')->insert([
            'name' => "lunch"
        ]);

        DB::table('package_facilities')->insert([
            'name' => "4 O'Clock Tea"
        ]);

        DB::table('package_facilities')->insert([
            'name' => "Dinner"
        ]);

        DB::table('package_facilities')->insert([
            'name' => "Field A"
        ]);

        DB::table('package_facilities')->insert([
            'name' => "Field B"
        ]);
    }
}
