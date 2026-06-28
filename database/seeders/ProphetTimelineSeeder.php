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
        $this->seedIbrahim();
        $this->seedMusa();
        $this->seedYusuf();

        // Add new prophet timeline methods below as we complete each story.
        // Example: $this->seedMusa();

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

    private function seedIbrahim(): void
    {
        $prophet = Prophet::where('slug', 'ibrahim')->first();
        if (!$prophet) {
            $this->command->warn('⚠️  Prophet "ibrahim" not found, skipped.');
            return;
        }

        $prophet->update([
            'timeline' => [
                [
                    'title' => "Disputing with His Father",
                    'description' => "Ibrahim gently called his father away from idol worship, warning him of Allah's punishment.",
                    'period' => 'Early Calling',
                    'order' => 1,
                ],
                [
                    'title' => 'Breaking the Idols',
                    'description' => 'He shattered his people\'s idols, exposing the absurdity of worshipping what cannot speak.',
                    'period' => 'Confrontation',
                    'order' => 2,
                ],
                [
                    'title' => 'Thrown into the Fire',
                    'description' => 'Sentenced to burn for his defiance, Allah commanded the fire to be coolness and safety upon him.',
                    'period' => 'Miracle',
                    'order' => 3,
                ],
                [
                    'title' => 'Seeking Certainty',
                    'description' => 'He debated a tyrant king about life and death, and asked Allah to show him how the dead are revived.',
                    'period' => 'Deepening Faith',
                    'order' => 4,
                ],
                [
                    'title' => 'Settling His Family Near the Sacred House',
                    'description' => 'He left his descendants in a barren valley near the Ka\'bah, trusting Allah completely for their provision.',
                    'period' => 'Trust',
                    'order' => 5,
                ],
                [
                    'title' => 'The Sacrifice of Ismail',
                    'description' => 'Commanded in a vision to sacrifice his son, both father and son submitted fully — until Allah ransomed Ismail.',
                    'period' => 'The Trial',
                    'order' => 6,
                ],
                [
                    'title' => "Building the Ka'bah",
                    'description' => 'Ibrahim and Ismail raised the foundations of the House of Allah and prayed for their descendants.',
                    'period' => 'Legacy',
                    'order' => 7,
                ],
                [
                    'title' => 'Khalilullah — Friend of Allah',
                    'description' => 'His complete submission earned him a title unlike any other prophet: the intimate Friend of Allah.',
                    'period' => 'Honor',
                    'order' => 8,
                ],
            ],
        ]);

        $this->command->info('  ✅ Ibrahim timeline seeded.');
    }

    private function seedMusa(): void
    {
        $prophet = Prophet::where('slug', 'musa')->first();
        if (!$prophet) {
            $this->command->warn('⚠️  Prophet "musa" not found, skipped.');
            return;
        }

        $prophet->update([
            'timeline' => [
                [
                    'title' => 'Born Under Threat of Death',
                    'description' => 'Musa was born as Pharaoh slaughtered the sons of the Israelites, and his mother set him afloat on the river in trust of Allah.',
                    'period' => 'Birth',
                    'order' => 1,
                ],
                [
                    'title' => 'Raised in Pharaoh\'s House',
                    'description' => 'Allah returned Musa to his mother\'s care, and he grew up within the very household of his people\'s oppressor.',
                    'period' => 'Upbringing',
                    'order' => 2,
                ],
                [
                    'title' => 'Flight to Midian',
                    'description' => 'After a fatal altercation, Musa fled Egypt in fear, and Allah guided him safely to Midian.',
                    'period' => 'Exile',
                    'order' => 3,
                ],
                [
                    'title' => 'The Call at Mount Tur',
                    'description' => 'Allah spoke directly to Musa from the sacred valley, granting him prophethood and the staff and hand as signs.',
                    'period' => 'Prophethood',
                    'order' => 4,
                ],
                [
                    'title' => 'Confronting Pharaoh',
                    'description' => 'Musa and Harun delivered Allah\'s message to Pharaoh, calling him to release the Israelites and worship the One God.',
                    'period' => 'Confrontation',
                    'order' => 5,
                ],
                [
                    'title' => 'The Magicians\' Contest',
                    'description' => 'Pharaoh\'s sorcerers were defeated when Allah caused Musa\'s staff to overcome their illusions, and the magicians believed.',
                    'period' => 'Sign',
                    'order' => 6,
                ],
                [
                    'title' => 'The Exodus and the Red Sea',
                    'description' => 'Allah split the sea for Musa and the believers to cross, and drowned Pharaoh and his army as they pursued.',
                    'period' => 'Deliverance',
                    'order' => 7,
                ],
                [
                    'title' => 'Trials in the Wilderness',
                    'description' => 'Despite Allah\'s provision of manna and quails, the people complained, and among them Qarun\'s arrogance led to his destruction.',
                    'period' => 'Testing',
                    'order' => 8,
                ],
                [
                    'title' => 'The Journey with Al-Khidr',
                    'description' => 'Musa sought knowledge from a righteous servant of Allah, learning patience through three events whose true wisdom was hidden from him.',
                    'period' => 'Humility',
                    'order' => 9,
                ],
            ],
        ]);

        $this->command->info('  ✅ Musa timeline seeded.');
    }


    private function seedYusuf(): void
    {
        $prophet = Prophet::where('slug', 'yusuf')->first();
        if (!$prophet) {
            $this->command->warn('⚠️  Prophet "yusuf" not found, skipped.');
            return;
        }

        $prophet->update([
            'timeline' => [
                [
                    'title' => 'The Dream of Eleven Stars',
                    'description' => 'Young Yusuf saw a vision of eleven stars, the sun, and the moon prostrating to him.',
                    'period' => 'Divine Sign',
                    'order' => 1,
                ],
                [
                    'title' => 'Thrown into the Well',
                    'description' => 'His jealous brothers cast him into a well, abandoning him to despair.',
                    'period' => 'Betrayal',
                    'order' => 2,
                ],
                [
                    'title' => 'Sold into Slavery',
                    'description' => 'A caravan found him and sold him as a slave in Egypt for a small price.',
                    'period' => 'Captivity',
                    'order' => 3,
                ],
                [
                    'title' => 'In the House of al-Azeez',
                    'description' => 'Yusuf served the Egyptian official with honesty and excellence, earning complete trust.',
                    'period' => 'Faithfulness',
                    'order' => 4,
                ],
                [
                    'title' => 'False Accusation',
                    'description' => 'The wife of al-Azeez falsely accused him of seduction when he refused her advances.',
                    'period' => 'Trial of Innocence',
                    'order' => 5,
                ],
                [
                    'title' => 'Years in Prison',
                    'description' => 'Imprisoned for a crime he did not commit, Yusuf remained patient and steadfast in faith.',
                    'period' => 'Patience',
                    'order' => 6,
                ],
                [
                    'title' => 'The King\'s Dream Interpreted',
                    'description' => 'Released from prison to interpret the king\'s dream of seven fat and seven lean cows.',
                    'period' => 'Recognition',
                    'order' => 7,
                ],
                [
                    'title' => 'Appointed Over the Treasures',
                    'description' => 'The king honored him and placed him in charge of Egypt\'s storehouses.',
                    'period' => 'Authority',
                    'order' => 8,
                ],
                [
                    'title' => 'His Brothers Arrive in Egypt',
                    'description' => 'During the famine, his brothers came to buy grain, not recognizing him.',
                    'period' => 'Recognition Deferred',
                    'order' => 9,
                ],
                [
                    'title' => 'The Planted Cup Test',
                    'description' => 'He tested his brothers by secretly placing a golden cup in Benjamin\'s saddlebag.',
                    'period' => 'Divine Trial',
                    'order' => 10,
                ],
                [
                    'title' => 'Benjamin Held in Egypt',
                    'description' => 'His youngest brother became his companion while the others despaired.',
                    'period' => 'Sorrow and Hope',
                    'order' => 11,
                ],
                [
                    'title' => 'Yusuf Reveals Himself',
                    'description' => 'He declared his identity to his brothers: "I am Joseph, and this is my brother."',
                    'period' => 'The Reveal',
                    'order' => 12,
                ],
                [
                    'title' => 'Complete Forgiveness',
                    'description' => 'He forgave his brothers entirely, saying "No blame will there be upon you today."',
                    'period' => 'Mercy',
                    'order' => 13,
                ],
                [
                    'title' => 'Yaqub\'s Sight Restored',
                    'description' => 'His father\'s eyes, white with grief for decades, regained sight when Yusuf\'s shirt touched them.',
                    'period' => 'Divine Healing',
                    'order' => 14,
                ],
                [
                    'title' => 'The Family Reunited in Egypt',
                    'description' => 'His entire family — parents, brothers, and their households — came to live under his care.',
                    'period' => 'Reunion',
                    'order' => 15,
                ],
                [
                    'title' => 'The Dream Fulfilled',
                    'description' => 'His parents and siblings bowed before him, exactly as his dream from childhood had foretold.',
                    'period' => 'Divine Promise Realized',
                    'order' => 16,
                ],
                [
                    'title' => 'Dua for the Hereafter',
                    'description' => 'At the height of his worldly honor, he prayed to die as a Muslim and be joined with the righteous.',
                    'period' => 'Spiritual Completion',
                    'order' => 17,
                ],
            ],
        ]);

        $this->command->info('  ✅ Yusuf timeline seeded.');
    }

    // ---------------------------------------------------------------
    // Template for the next prophet — copy this pattern when ready:
    //
    // private function seedMusa(): void
    // {
    //     $prophet = Prophet::where('slug', 'musa')->first();
    //     if (!$prophet) {
    //         $this->command->warn('⚠️  Prophet "musa" not found, skipped.');
    //         return;
    //     }
    //
    //     $prophet->update([
    //         'timeline' => [
    //             ['title' => '...', 'description' => '...', 'period' => '...', 'order' => 1],
    //         ],
    //     ]);
    //
    //     $this->command->info('  ✅ Musa timeline seeded.');
    // }
    // ---------------------------------------------------------------
}
