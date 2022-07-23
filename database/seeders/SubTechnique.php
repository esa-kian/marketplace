<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubTechnique extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mitres')->insert([
            ['parent_id' => 17, 'mitre_num' => 'T1059.001', 'name' => 'Spearphishing Attachment', 'type' => 'sub_technique'],
            ['parent_id' => 17, 'mitre_num' => 'T1059.002', 'name' => 'Spearphishing Link', 'type' => 'sub_technique'],
            ['parent_id' => 17, 'mitre_num' => 'T1059.003', 'name' => 'Spearphishing via Service' , 'type' => 'sub_technique'],
            ['parent_id' => 19, 'mitre_num' => 'T1559.001', 'name' => 'Compromise Software Dependencies and Development Tools' , 'type' => 'sub_technique'],
            ['parent_id' => 19, 'mitre_num' => 'T1559.002', 'name' => 'Compromise Software Supply Chain' , 'type' => 'sub_technique'],
            ['parent_id' => 19, 'mitre_num' => 'T1559.003', 'name' => 'Compromise Hardware Supply Chain' , 'type' => 'sub_technique'],
            ['parent_id' => 18, 'mitre_num' => 'T1203.001', 'name' => 'Default Accounts' , 'type' => 'sub_technique'],
            ['parent_id' => 18, 'mitre_num' => 'T1203.002', 'name' => 'Domain Accounts' , 'type' => 'sub_technique'],
            ['parent_id' => 18, 'mitre_num' => 'T1203.003', 'name' => 'Local Accounts' , 'type' => 'sub_technique'],
            ['parent_id' => 18, 'mitre_num' => 'T1203.004', 'name' => 'Cloud Accounts' , 'type' => 'sub_technique'],

        ]);
    }
}
