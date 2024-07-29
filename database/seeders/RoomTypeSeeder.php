<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('room_types')->insert([
            'id' => 1,
            'type' => 'Standard',
        ]);

        DB::table('room_types')->insert([
            'id' => 2,
            'type' => 'Cottage',
        ]);

        DB::table('room_types')->insert([
            'id' => 3,
            'type' => 'Deluxe',
        ]);

        DB::table('room_types')->insert([
            'id' => 4,
            'type' => 'Deluxe Twin',
        ]);

        DB::table('room_types')->insert([
            'id' => 5,
            'type' => 'Conference Hall',
        ]);

    }
}
