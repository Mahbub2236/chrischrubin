<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BeneficiaireSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('beneficiaire')->insert([
            [
                'beneficiaireId' => 1,
                'userId' => 1589874, 
                'benId'  => 2,
                'date'   => now(),
            ],
            [
                'beneficiaireId' => 2,
                'userId' => 198574, 
                'benId'  => 1, 
                'date'   => now(),
            ],
            [
                'beneficiaireId' => 3,
                'userId' => 1589874,
                'benId'  => 3, 
                'date'   => now(),
            ]
        ]);
    }
}