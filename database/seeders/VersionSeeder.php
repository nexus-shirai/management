<?php

namespace Database\Seeders;

use App\Models\Version;
use Illuminate\Database\Seeder;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $versions = [
            [
                "version_name" => "0.8"
            ],
            [
                "version_name" => "0.9"
            ],
            [
                "version_name" => "0.10"
            ],
            [
                "version_name" => "1.2"
            ],
        ];

        foreach ($versions as $version) {
            Version::insert([
                "version_name" => $version["version_name"]
            ]);
        }
    }
}
