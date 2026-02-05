<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('user')->insert([
            [
                'userID' => 1589874,
                'nprenom' => 'Haris Sultan',
                'fname' => 'Haris',
                'email' => 'haris@example.com',
                'phone' => '923145205606',
                'mdp' => 'hash_password_here',
                'pays' => 'PK',
                'solde' => 100.00,
                'statut' => '1',
                'created_at' => now(),
            ],
            [
                'userID' => 198574,
                'nprenom' => 'Chris Cherubin',
                'fname' => 'Chris',
                'email' => 'chris@example.com',
                'phone' => '4385304098',
                'mdp' => 'hash_password_here',
                'pays' => 'HT',
                'solde' => 50.00,
                'statut' => '1',
                'created_at' => now(),
            ]
        ]);
    }
}