<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prophet;

class ProphetTimelineSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdam();
        $this->seedNuh();

        // Add new prophet timeline methods below as we complete each story.
        // Example: $this->seedIbrahim();

        $this->command->info('✅ Prophet timelines seeded.');
    }

    private function seedAdam(): void
    {
        $prophet = Prophet::where('slug', 'adam')->first();
        if (!$prophet) {
            $this->command->warn('⚠️  Prophet "adam" not found, skipped.');
            return;
        }

        $prophet->update([
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
                    'description' => 'Allah accepted Adam\'s sincere repentance.',
                    'period' => 'Mercy',
                    'order' => 7,
                ],
                [
                    'title' => 'First Prophet',
                    'description' => 'Adam guided his children to worship Allah.',
                    'period' => 'Prophethood',
                    'order' => 8,
                ],
            ],
        ]);

        $this->command->info('  ✅ Adam timeline seeded.');
    }

    private function seedNuh(): void
    {
        $prophet = Prophet::where('slug', 'nuh')->first();
        if (!$prophet) {
            $this->command->warn('⚠️  Prophet "nuh" not found, skipped.');
            return;
        }

        $prophet->update([
            'timeline' => [
                [
                    'title' => 'Sent to His People',
                    'description' => 'Allah sent Nuh to warn his people who had turned to idol worship.',
                    'period' => 'Mission Begins',
                    'order' => 1,
                ],
                [
                    'title' => '950 Years of Calling',
                    'description' => 'Nuh called his people night and day, publicly and privately, for nearly a thousand years.',
                    'period' => 'Patience',
                    'order' => 2,
                ],
                [
                    'title' => 'The Command to Build the Ark',
                    'description' => 'Allah commanded Nuh to build a ship under His guidance and protection.',
                    'period' => 'Preparation',
                    'order' => 3,
                ],
                [
                    'title' => 'Mocked While Building',
                    'description' => 'His own people ridiculed him as he built the ship far from any sea.',
                    'period' => 'Trial',
                    'order' => 4,
                ],
                [
                    'title' => 'The Great Flood',
                    'description' => 'Water burst from the sky and the earth, and the flood overtook the wrongdoers.',
                    'period' => 'Punishment',
                    'order' => 5,
                ],
                [
                    'title' => 'His Son Refused to Board',
                    'description' => 'Nuh called his son to the ark, but he chose to trust a mountain instead of Allah.',
                    'period' => 'Sorrow',
                    'order' => 6,
                ],
                [
                    'title' => 'The Ark Rests on Judiyy',
                    'description' => 'The waters subsided and the ship came to rest on Mount Judiyy.',
                    'period' => 'Relief',
                    'order' => 7,
                ],
                [
                    'title' => 'Humble Correction',
                    'description' => 'Nuh sought Allah\'s forgiveness for asking about something beyond his knowledge.',
                    'period' => 'Humility',
                    'order' => 8,
                ],
            ],
        ]);

        $this->command->info('  ✅ Nuh timeline seeded.');
    }

    // ---------------------------------------------------------------
    // Template for the next prophet — copy this pattern when ready:
    //
    // private function seedIbrahim(): void
    // {
    //     $prophet = Prophet::where('slug', 'ibrahim')->first();
    //     if (!$prophet) {
    //         $this->command->warn('⚠️  Prophet "ibrahim" not found, skipped.');
    //         return;
    //     }
    //
    //     $prophet->update([
    //         'timeline' => [
    //             ['title' => '...', 'description' => '...', 'period' => '...', 'order' => 1],
    //         ],
    //     ]);
    //
    //     $this->command->info('  ✅ Ibrahim timeline seeded.');
    // }
    // ---------------------------------------------------------------
}
