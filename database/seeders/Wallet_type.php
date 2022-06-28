<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class Wallet_type extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('wallet_types')->insert(


            [
                'name' => 'LOAN',
                'min_amount' => '2000',
                'monthly_interest_rate' => '10',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'CREDIT',
                'min_amount' => '3000',
                'monthly_interest_rate' => '10',
                'created_at' => now(),
                'updated_at' => now()
            ]

        );
    }
}
