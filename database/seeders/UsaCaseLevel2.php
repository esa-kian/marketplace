<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsaCaseLevel2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('use_cases')->insert([
            // Level 2
            ['title' => 'Port Scanning', 'parent_id' => 1, 'type' => 'Level2'],
            ['title' => 'Mail probing', 'parent_id' => 1, 'type' => 'Level2'],
            ['title' => 'Fingerprinting', 'parent_id' => 1, 'type' => 'Level2'],
            ['title' => 'Passive reconnaissance', 'parent_id' => 1, 'type' => 'Level2'],
            ['title' => 'Brute force', 'parent_id' => 1, 'type' => 'Level2'],
            ['title' => 'Social Engineering', 'parent_id' => 1, 'type' => 'Level2'],
            ['title' => 'Social media harvesting', 'parent_id' => 1, 'type' => 'Level2'],
            ['title' => 'Web based malware delivery', 'parent_id' => 1, 'type' => 'Level2'],

            ['title' => 'Web based malware delivery', 'parent_id' => 2, 'type' => 'Level2'],
            ['title' => 'Email based malware delivery', 'parent_id' => 2, 'type' => 'Level2'],
            ['title' => 'Physical malware delivery', 'parent_id' => 2, 'type' => 'Level2'],
            ['title' => 'Widespread malware outbreak', 'parent_id' => 2, 'type' => 'Level2'],
            ['title' => 'Worm propagation', 'parent_id' => 2, 'type' => 'Level2'],
            ['title' => 'Delivery of phishing mail', 'parent_id' => 2, 'type' => 'Level2'],
            ['title' => 'Delivery of Remote Access Tool', 'parent_id' => 2, 'type' => 'Level2'],

            ['title' => 'Application whitelist evasion attempt', 'parent_id' => 3, 'type' => 'Level2'],
            ['title' => 'Network-based attack', 'parent_id' => 3, 'type' => 'Level2'],
            ['title' => 'Brute force exploitation attempt', 'parent_id' => 3, 'type' => 'Level2'],
            ['title' => 'Network intrusion attempt', 'parent_id' => 3, 'type' => 'Level2'],
            ['title' => 'Application intrusion attempt', 'parent_id' => 3, 'type' => 'Level2'],

            ['title' => 'Installation of Remote Access Tool or similar backdoor', 'parent_id' => 4, 'type' => 'Level2'],
            ['title' => 'Installation of malware', 'parent_id' => 4, 'type' => 'Level2'],

            ['title' => 'Suspicious outbound network communication', 'parent_id' => 5, 'type' => 'Level2'],
            ['title' => 'Covert channel', 'parent_id' => 5, 'type' => 'Level2'],

            ['title' => 'Data collection', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Data exfiltration', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Credential theft', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Internal reconnaissance', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Lateral movement', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Internal Brute force', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Privilege escalation', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'File corruption, encryption and unauthorized access', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Account breached', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Installation of persistence mechanism', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Database compromise attempt', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Detection evasion techniques', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Cloud Service or Account compromise', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Usage of remote access tool', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Rogue network device or service', 'parent_id' => 6, 'type' => 'Level2'],
            ['title' => 'Multiple AO categories', 'parent_id' => 6, 'type' => 'Level2'],

            ['title' => 'Extortion attempts', 'parent_id' => 7, 'type' => 'Level2'],
            ['title' => 'CEO Fraud', 'parent_id' => 7, 'type' => 'Level2'],

            ['title' => 'Volume DDoS', 'parent_id' => 8, 'type' => 'Level2'],
            ['title' => 'Protocol (D)Dos', 'parent_id' => 8, 'type' => 'Level2'],
            ['title' => 'Application (D)DoS', 'parent_id' => 8, 'type' => 'Level2'],

            ['title' => 'Illegal entry into datacenter', 'parent_id' => 9, 'type' => 'Level2'],
            ['title' => 'Illegal entry into office space', 'parent_id' => 9, 'type' => 'Level2'],
            ['title' => 'Mobile device lost or stolen', 'parent_id' => 9, 'type' => 'Level2'],
            ['title' => 'Anomalous access token usage', 'parent_id' => 9, 'type' => 'Level2'],
            ['title' => 'Physical network access compromise', 'parent_id' => 9, 'type' => 'Level2'],

            ['title' => 'IP blacklisting', 'parent_id' => 10, 'type' => 'Level2'],
            ['title' => 'Mail server blacklisting', 'parent_id' => 10, 'type' => 'Level2'],

            ['title' => 'Cyber sabotage', 'parent_id' => 11, 'type' => 'Level2'],
            ['title' => 'Physical sabotage', 'parent_id' => 11, 'type' => 'Level2'],
            ['title' => 'Defacement', 'parent_id' => 11, 'type' => 'Level2'],

            ['title' => 'Illegal content streaming or usage of disallowed web service', 'parent_id' => 12, 'type' => 'Level2'],
            ['title' => 'Insecure communication protocol', 'parent_id' => 12, 'type' => 'Level2'],
            ['title' => 'Vulnerable system or service', 'parent_id' => 12, 'type' => 'Level2'],
            ['title' => 'Unauthorized process launched', 'parent_id' => 12, 'type' => 'Level2'],
            ['title' => 'Illegal or aberrant application found on host', 'parent_id' => 12, 'type' => 'Level2'],
            ['title' => 'Resource abuse', 'parent_id' => 12, 'type' => 'Level2'],
            ['title' => 'Unauthorized use of high-privileged account', 'parent_id' => 12, 'type' => 'Level2'],
            ['title' => 'Unauthorized modification of technical policy', 'parent_id' => 12, 'type' => 'Level2'],
        ]);
    }
}
