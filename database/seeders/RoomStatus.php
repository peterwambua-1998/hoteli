<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomStatus extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('room_statuses')->insert([
            'id' => 1,
            'name' => 'Vacant',
        ]);

        DB::table('room_statuses')->insert([
            'id' => 2,
            'name' => 'Occupied',
        ]);
    }
}
