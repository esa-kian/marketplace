<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Tactic extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mitres')->insert([
            ['id' => 1, 'mitre_num' => 'TA0001', 'name' => 'Initial Access', 'type' => 'tactic'],
            ['id' => 2, 'mitre_num' => 'TA0002', 'name' => 'Execution', 'type' => 'tactic'],
            ['id' => 3, 'mitre_num' => 'TA0003', 'name' => 'Persistence', 'type' => 'tactic'],
            ['id' => 4, 'mitre_num' => 'TA0004', 'name' => 'Privilege Escalation', 'type' => 'tactic'],
            ['id' => 5, 'mitre_num' => 'TA0005', 'name' => 'Defense Evasion', 'type' => 'tactic'],
            ['id' => 6, 'mitre_num' => 'TA0006', 'name' => 'Credential Access', 'type' => 'tactic'],
            ['id' => 7, 'mitre_num' => 'TA0007', 'name' => 'Discovery', 'type' => 'tactic'],
            ['id' => 8, 'mitre_num' => 'TA0008', 'name' => 'Lateral Movement', 'type' => 'tactic'],
            ['id' => 9, 'mitre_num' => 'TA0009', 'name' => 'Collection', 'type' => 'tactic'],
            ['id' => 10, 'mitre_num' => 'TA0011', 'name' => 'Command and Control', 'type' => 'tactic'],
            ['id' => 11, 'mitre_num' => 'TA0010', 'name' => 'Exfiltration', 'type' => 'tactic'],
            ['id' => 12, 'mitre_num' => 'TA0012', 'name' => 'Impact', 'type' => 'tactic'],
        ]);
    }
}
