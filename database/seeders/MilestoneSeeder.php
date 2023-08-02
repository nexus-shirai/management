<?php

namespace Database\Seeders;

use App\Models\Milestone;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $milestones = [
            [
                "milestone_name" => "企画フェーズ"
            ],
            [
                "milestone_name" => "設計フェーズ"
            ],
            [
                "milestone_name" => "試作フェーズ"
            ],
            [
                "milestone_name" => "量産フェーズ"
            ],
            [
                "milestone_name" => "出荷フェーズ"
            ]
        ];

        foreach ($milestones as $milestone) {
            milestone::insert([
                "milestone_name" => $milestone["milestone_name"]
            ]);
        }
    }
}
