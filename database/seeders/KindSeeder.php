<?php

namespace Database\Seeders;

use App\Models\Kind;
use Illuminate\Database\Seeder;

class KindSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kinds = [
            [
                "kind_name" => "バグ",
                "kind_desc" => "問題やバグ・不具合等。",
                "hex_color" => "#F07756"
            ],
            [
                "kind_name" => "タスク",
                "kind_desc" => "やるべき作業項目やToDo。",
                "hex_color" => "#B7CA22"
            ],
            [
                "kind_name" => "要要望",
                "kind_desc" => "要望・質問等。",
                "hex_color" => "#E0A704"
            ],
            [
                "kind_name" => "その他",
                "kind_desc" => "上記以外のもの。",
                "hex_color" => "#1C9CC3"
            ],
        ];

        foreach ($kinds as $kind) {
            Kind::insert([
                "kind_name" => $kind["kind_name"],
                "kind_desc" => $kind["kind_desc"],
                "hex_color" => $kind["hex_color"]
            ]);
        }
    }
}
