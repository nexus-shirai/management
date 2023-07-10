<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                "status_name" => "未対応",
                "hex_color" => "#F18077"
            ],
            [
                "status_name" => "処理中",
                "hex_color" => "#2289C5"
            ],
            [
                "status_name" => "処理済み",
                "hex_color" => "#5CB5A6"
            ],
            [
                "status_name" => "保留",
                "hex_color" => "#808DB7"
            ],
            [
                "status_name" => "完了",
                "hex_color" => "#B9BD3F"
            ]
        ];

        foreach ($statuses as $status) {
            Status::insert([
                "status_name" => $status["status_name"],
                "hex_color" => $status["hex_color"]
            ]);
        }
    }
}
