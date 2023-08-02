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
        $this->call(UserSeeder::class);
        $this->call(KindSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(MilestoneSeeder::class);
        $this->call(VersionSeeder::class);
        $this->call(IssueSeeder::class);
    }
}
