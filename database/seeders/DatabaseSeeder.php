<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();
        $this->call(UseCase::class);
        $this->call(UsaCaseLevel2::class);
        $this->call(SmCat::class);
        $this->call(Tactic::class);
        $this->call(Technique::class);
        $this->call(SubTechnique::class);
    }
}
