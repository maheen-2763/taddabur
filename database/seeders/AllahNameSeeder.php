<?php

namespace Database\Seeders;

use App\Models\AllahName;
use Illuminate\Database\Seeder;

class AllahNameSeeder extends Seeder
{
    public function run(): void
    {
        $names = require database_path('data/allah_names.php');

        foreach ($names as $name) {
            AllahName::updateOrCreate(
                ['position' => $name['position']],
                $name
            );
        }
    }
}
