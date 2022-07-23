<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UseCase extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('use_cases')->insert([
            // Level 1
            ['id' => 1, 'title' => 'Reconnaissance', 'type' => 'Level1'],
            ['id' => 2, 'title' => 'Delivery', 'type' => 'Level1'],
            ['id' => 3, 'title' => 'Exploitation', 'type' => 'Level1'],
            ['id' => 4, 'title' => 'Installation', 'type' => 'Level1'],
            ['id' => 5, 'title' => 'Command & Control', 'type' => 'Level1'],
            ['id' => 6, 'title' => 'Actions on Objectives', 'type' => 'Level1'],
            ['id' => 7, 'title' => 'Fraud / Extortion', 'type' => 'Level1'],
            ['id' => 8, 'title' => '(D)DoS', 'type' => 'Level1'],
            ['id' => 9, 'title' => 'Physical Access Compromise', 'type' => 'Level1'],
            ['id' => 10, 'title' => 'Blacklisting', 'type' => 'Level1'],
            ['id' => 11, 'title' => 'Sabotage / Destruction', 'type' => 'Level1'],
            ['id' => 12, 'title' => 'Policy Violations', 'type' => 'Level1'],
        ]);
    }
}
