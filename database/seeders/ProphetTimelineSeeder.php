<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prophet;

class ProphetTimelineSeeder extends Seeder
{
    public function run(): void
    {
        $adam = Prophet::where('slug', 'adam')->first();

        if (!$adam) {
            return;
        }

        $adam->update([
            'timeline' => [
                [
                    'title' => 'Creation of Adam',
                    'description' => 'Allah created Adam from clay.',
                    'period' => 'Beginning',
                    'order' => 1,
                ],
                [
                    'title' => 'The Angels Prostrate',
                    'description' => 'The angels prostrated to Adam except Iblis.',
                    'period' => 'Honor',
                    'order' => 2,
                ],
                [
                    'title' => 'Knowledge of the Names',
                    'description' => 'Allah taught Adam the names of all things.',
                    'period' => 'Knowledge',
                    'order' => 3,
                ],
                [
                    'title' => 'Life in Paradise',
                    'description' => 'Adam and Hawwa lived in Jannah.',
                    'period' => 'Jannah',
                    'order' => 4,
                ],
                [
                    'title' => 'The Test',
                    'description' => 'They were instructed not to approach the tree.',
                    'period' => 'Test',
                    'order' => 5,
                ],
                [
                    'title' => 'Descent to Earth',
                    'description' => 'Adam and Hawwa were sent to Earth.',
                    'period' => 'Earth',
                    'order' => 6,
                ],
                [
                    'title' => 'Repentance Accepted',
                    'description' => 'Allah accepted Adam’s sincere repentance.',
                    'period' => 'Mercy',
                    'order' => 7,
                ],
                [
                    'title' => 'First Prophet',
                    'description' => 'Adam guided his children to worship Allah.',
                    'period' => 'Prophethood',
                    'order' => 8,
                ],
            ]
        ]);
    }
}
