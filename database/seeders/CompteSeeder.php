<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('compte')->insert([
            [
                'userId'     => 1589874,
                'pays'       => 'HT',
                'banque'     => 'BNC',
                'type'       => 'Visa',
                'monnaie'    => 'USD',
                'numero'     => 'pm_1QrwUo2eZvKYlo2CuX7pA1',
                'routing'    => '0110001',
                'date'       => now(),
                'lastdigit'  => '4242',
                'expiration' => '1226',
                'compte'     => 'Primary Card',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'userId'     => 1589874, 
                'pays'       => 'HT',
                'banque'     => 'SOG',
                'type'       => 'Mast',
                'monnaie'    => 'USD',
                'numero'     => 'pm_1QrwUo2eZvKYlo2CuX7pB2',
                'routing'    => '0220002',
                'date'       => now(),
                'lastdigit'  => '5555',
                'expiration' => '0825',
                'compte'     => 'Business Card',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}