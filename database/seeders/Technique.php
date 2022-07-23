<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Technique extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mitres')->insert([
            ['parent_id' => 1, 'mitre_num' => 'T1189', 'name' => 'Drive-by Compromise', 'type' => 'technique'],
            ['parent_id' => 1, 'mitre_num' => 'T1190', 'name' => 'Exploit Public-Facing Application', 'type' => 'technique'],
            ['parent_id' => 1, 'mitre_num' => 'T1133', 'name' => 'External Remote Services', 'type' => 'technique'],
            ['parent_id' => 1, 'mitre_num' => 'T1200', 'name' => 'Hardware Additions', 'type' => 'technique'],

            ['parent_id' => 2, 'mitre_num' => 'T1059', 'name' => 'Command and Scripting Interpreter', 'type' => 'technique'],
            ['parent_id' => 2, 'mitre_num' => 'T1203', 'name' => 'Exploitation for Client Execution', 'type' => 'technique'],
            ['parent_id' => 2, 'mitre_num' => 'T1559', 'name' => 'Inter-Process Communication', 'type' => 'technique'],



        ]);
    }
}
