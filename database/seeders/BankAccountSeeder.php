<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bank_accounts')->insert([
            'bank_name' => 'Cash',
            'account_name' => 'Cash',
            'account_number' => '00000',
            'branch' => 'Isolo',
            'available_balance' => '0',
        ]);
    }
}
