<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\IssueCategory;
// use App\Models\ProjectUser;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $issues = [
            [
                "project_id" => 1,
                "issue_cd" => "PROJ_01_1",
                "issue_title" => "課題名01",
                "issue_desc" => "課題内容",
                "issue_priority" => Issue::PRIORITY_LOW,
                "version_id" => 1,
                "estimated_time" => 1,
                "completion_time" => 2,
                "complete_reason" => NULL,
                "assignee_id" => 2,
                "milestone_id" => 1,
                "start_date" => now(),
                "end_date" => now(),
                "status_id" => 1,
                "kind_id" => 1,
                "issue_rank" => Issue::RANK_PARENT,
                "parent_issue_id" => 1,
                "create_user_id" => 1,
                "issue_categories" => [1, 2, 3, 4],
            ],
            [
                "project_id" => 1,
                "issue_cd" => "PROJ_01_2",
                "issue_title" => "課題名02",
                "issue_desc" => "課題内容",
                "issue_priority" => Issue::PRIORITY_MEDIUM,
                "version_id" => 2,
                "estimated_time" => 2,
                "completion_time" => 3,
                "complete_reason" => NULL,
                "assignee_id" => 3,
                "milestone_id" => 2,
                "start_date" => now(),
                "end_date" => now(),
                "status_id" => 2,
                "kind_id" => 2,
                "issue_rank" => Issue::RANK_PARENT,
                "parent_issue_id" => 1,
                "create_user_id" => 2,
                "issue_categories" => [1, 2],
            ],
            [
                "project_id" => 1,
                "issue_cd" => "PROJ_01_3",
                "issue_title" => "課題名03",
                "issue_desc" => "課題内容",
                "issue_priority" => Issue::PRIORITY_HIGH,
                "version_id" => 3,
                "estimated_time" => 3,
                "completion_time" => 4,
                "complete_reason" => NULL,
                "assignee_id" => 4,
                "milestone_id" => 3,
                "start_date" => now(),
                "end_date" => now(),
                "status_id" => 3,
                "kind_id" => 3,
                "issue_rank" => Issue::RANK_PARENT,
                "parent_issue_id" => 1,
                "create_user_id" => 2,
                "issue_categories" => [1],
            ],
        ];

        foreach ($issues as $issue) {
            $issueData = $issue;
            unset($issueData["issue_categories"]);
            $issueData = Issue::create($issueData);

            foreach ($issue["issue_categories"] as $issue_category) {
                IssueCategory::insert([
                    "issue_id" => $issueData["project_id"],
                    "category_id" => $issue_category
                ]);
            }
        }
    }
}
