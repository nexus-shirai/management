<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectUser;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projects = [
            [
                "project_cd" => "PROJ_01",
                "project_name" => "案件01",
                "project_desc" => "プロジェクト概要",
                "project_users" => [2, 3, 4]
            ],
            [
                "project_cd" => "PROJ_02",
                "project_name" => "案件02",
                "project_desc" => "プロジェクト概要",
                "project_users" => [2, 3]
            ],
            [
                "project_cd" => "PROJ_03",
                "project_name" => "案件03",
                "project_desc" => "プロジェクト概要",
                "project_users" => [2]
            ],
        ];

        foreach ($projects as $project) {
            $projectData = Project::create([
                "project_cd" => $project["project_cd"],
                "project_name" => $project["project_name"],
                "project_desc" => $project["project_desc"]
            ]);

            foreach ($project["project_users"] as $project_user) {
                ProjectUser::insert([
                    "project_id" => $projectData["project_id"],
                    "user_id" => $project_user
                ]);
            }
        }
    }
}
