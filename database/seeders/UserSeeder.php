<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 1,
            'phone_num' => '+25472134711',
            'gender' => 'male',
        ]);

        DB::table('users')->insert([
            'name' => 'Cashier',
            'email' => 'cashier@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 3,
            'phone_num' => '+25472134711',
            'gender' => 'male',
        ]);

        // wait bar role  == 4
        DB::table('users')->insert([
            'name' => 'Waiter',
            'email' => 'w@g.com',
            'password' => Hash::make('12345678'),
            'role' => 4,
            'phone_num' => '+25472134711',
            'gender' => 'male',
        ]);

        // front office  == 5
        DB::table('users')->insert([
            'name' => 'Reception',
            'email' => 'front@mail.com',
            'password' => Hash::make('12345678'),
            'role' => 5,
            'phone_num' => '+25472134711',
            'gender' => 'male',
        ]);
    }
}
