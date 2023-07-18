<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\IssueCategory;
use Carbon\Carbon;
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
                "issue_title" => "親課題",
                "issue_desc" => "課題内容",
                "issue_priority" => Issue::PRIORITY_LOW,
                "version_id" => 1,
                "estimated_time" => 1,
                "completion_time" => 2,
                "complete_reason" => NULL,
                "assignee_id" => 4,
                "milestone_id" => 5,
                "start_date" => Carbon::now()->addDays(2),
                "end_date" => Carbon::now()->addDays(11),
                "status_id" => 1,
                "kind_id" => 1,
                "issue_rank" => Issue::RANK_PARENT,
                "parent_issue_id" => NULL,
                "create_user_id" => 1,
                "issue_categories" => [1, 2, 3, 4],
            ],
            [
                "project_id" => 1,
                "issue_cd" => "PROJ_01_2",
                "issue_title" => "子課題1",
                "issue_desc" => "課題内容",
                "issue_priority" => Issue::PRIORITY_MEDIUM,
                "version_id" => 2,
                "estimated_time" => 2,
                "completion_time" => 3,
                "complete_reason" => NULL,
                "assignee_id" => 2,
                "milestone_id" => 4,
                "start_date" => Carbon::now()->addDays(2),
                "end_date" => Carbon::now()->addDays(5),
                "status_id" => 2,
                "kind_id" => 2,
                "issue_rank" => Issue::RANK_CHILD,
                "parent_issue_id" => 1,
                "create_user_id" => 2,
                "issue_categories" => [1, 2],
            ],
            [
                "project_id" => 1,
                "issue_cd" => "PROJ_01_3",
                "issue_title" => "子課題2",
                "issue_desc" => "課題内容",
                "issue_priority" => Issue::PRIORITY_MEDIUM,
                "version_id" => 2,
                "estimated_time" => 2,
                "completion_time" => 3,
                "complete_reason" => NULL,
                "assignee_id" => 3,
                "milestone_id" => 3,
                "start_date" => Carbon::now()->addDays(3),
                "end_date" => Carbon::now()->addDays(4),
                "status_id" => 2,
                "kind_id" => 2,
                "issue_rank" => Issue::RANK_CHILD,
                "parent_issue_id" => 1,
                "create_user_id" => 2,
                "issue_categories" => [1, 2],
            ],
            [
                "project_id" => 1,
                "issue_cd" => "PROJ_01_4",
                "issue_title" => "孫課題1",
                "issue_desc" => "課題内容",
                "issue_priority" => Issue::PRIORITY_HIGH,
                "version_id" => 3,
                "estimated_time" => 3,
                "completion_time" => 4,
                "complete_reason" => NULL,
                "assignee_id" => 2,
                "milestone_id" => 2,
                "start_date" => Carbon::now()->addDays(2),
                "end_date" => Carbon::now()->addDays(4),
                "status_id" => 3,
                "kind_id" => 3,
                "issue_rank" => Issue::RANK_GRANDCHILD,
                "parent_issue_id" => 2,
                "create_user_id" => 2,
                "issue_categories" => [1],
            ],
            [
                "project_id" => 1,
                "issue_cd" => "PROJ_01_5",
                "issue_title" => "孫課題2",
                "issue_desc" => "課題内容",
                "issue_priority" => Issue::PRIORITY_HIGH,
                "version_id" => 3,
                "estimated_time" => 3,
                "completion_time" => 4,
                "complete_reason" => NULL,
                "assignee_id" => 2,
                "milestone_id" => 1,
                "start_date" => Carbon::now()->addDays(3),
                "end_date" => Carbon::now()->addDays(3),
                "status_id" => 3,
                "kind_id" => 3,
                "issue_rank" => Issue::RANK_GRANDCHILD,
                "parent_issue_id" => 2,
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
                    "issue_id" => $issueData["issue_id"],
                    "category_id" => $issue_category
                ]);
            }
        }
    }
}
