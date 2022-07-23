<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmCat extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('use_cases')->insert([
            ['title' => 'Malware Detection', 'type' => 'SMCAT'],
            ['title' => 'Asset (security) Monitoring', 'type' => 'SMCAT'],
            ['title' => 'Compliance Checking', 'type' => 'SMCAT'],
            ['title' => 'Data Exfiltration', 'type' => 'SMCAT'],
            ['title' => 'Web', 'type' => 'SMCAT'],
            ['title' => 'Policy Violation', 'type' => 'SMCAT'],
            ['title' => 'User Tracking', 'type' => 'SMCAT'],
            ['title' => 'Denial of Service', 'type' => 'SMCAT'],
            ['title' => 'Traffic Monitoring and Statistics', 'type' => 'SMCAT'],
            ['title' => 'Scanning and Reconnaissance', 'type' => 'SMCAT'],
            ['title' => 'DNS', 'type' => 'SMCAT'],
            ['title' => 'Brute Force', 'type' => 'SMCAT'],
            ['title' => 'Email', 'type' => 'SMCAT'],
            ['title' => 'Hacker/Worm/malware propagation', 'type' => 'SMCAT'],
            ['title' => 'Unauthorized Access', 'type' => 'SMCAT'],
            ['title' => 'Anomalous Ports, Services and Unpatched Hosts/Network Devices', 'type' => 'SMCAT'],
            ['title' => 'Privileged Abuse', 'type' => 'SMCAT'],
            ['title' => 'DataBase', 'type' => 'SMCAT'],
            ['title' => 'Backdoor, Persistence', 'type' => 'SMCAT'],
            ['title' => 'Exploitation', 'type' => 'SMCAT'],
            ['title' => 'Malicious Network Traffic', 'type' => 'SMCAT'],
            ['title' => 'Authentication', 'type' => 'SMCAT'],
            ['title' => 'Covert Channel Detection', 'type' => 'SMCAT'],
        ]);
    }
}
