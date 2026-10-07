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
        $this->seedIdris();
        $this->seedHud();
        $this->seedSalih();
        $this->seedHarun();
        $this->seedLut();
        $this->seedZakariyya();
        $this->seedYahya();
        $this->seedIsmail();
        $this->seedIshaq();
        $this->seedYaqub();
        $this->seedAyyub();
        $this->seedDhulKifl();
        $this->seedDawud();
        $this->seedSulayman();
        $this->seedIlyas();
        $this->seedAlYasa();
        $this->seedShuayb();
        $this->seedIsa();
        $this->seedYunus();

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
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Angels Prostrate',
                    'description' => 'The angels prostrated to Adam except Iblis.',
                    'period' => 'Honor',
                    'order' => 2,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Knowledge of the Names',
                    'description' => 'Allah taught Adam the names of all things.',
                    'period' => 'Knowledge',
                    'order' => 3,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Life in Paradise',
                    'description' => "Adam and his wife (traditionally known as Hawwa) lived in Jannah.",
                    'period' => 'Jannah',
                    'order' => 4,
                    'source' => 'tradition', // "Hawwa" name not in Quran; Quran says "your wife" (zawjaka)
                ],
                [
                    'title' => 'The Test',
                    'description' => 'They were instructed not to approach the tree.',
                    'period' => 'Test',
                    'order' => 5,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Descent to Earth',
                    'description' => "Adam and his wife (traditionally known as Hawwa) were sent to Earth.",
                    'period' => 'Earth',
                    'order' => 6,
                    'source' => 'tradition', // same Hawwa-name reason
                ],
                [
                    'title' => 'Repentance Accepted',
                    'description' => "Allah accepted Adam's sincere repentance.",
                    'period' => 'Mercy',
                    'order' => 7,
                    'source' => 'quran',
                ],
                [
                    'title' => 'First Prophet',
                    'description' => 'According to Hadith, Adam guided his children to worship Allah.',
                    'period' => 'Prophethood',
                    'order' => 8,
                    'source' => 'hadith', // Musnad Ahmad, Abu Dharr's narration
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
                    'source' => 'quran',
                ],
                [
                    'title' => '950 Years of Calling',
                    'description' => 'Nuh called his people night and day, publicly and privately, for nearly a thousand years.',
                    'period' => 'Patience',
                    'order' => 2,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Command to Build the Ark',
                    'description' => 'Allah commanded Nuh to build a ship under His guidance and protection.',
                    'period' => 'Preparation',
                    'order' => 3,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Mocked While Building',
                    'description' => 'His own people ridiculed him as he built the ship far from any sea.',
                    'period' => 'Trial',
                    'order' => 4,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Great Flood',
                    'description' => 'Water burst from the sky and the earth, and the flood overtook the wrongdoers.',
                    'period' => 'Punishment',
                    'order' => 5,
                    'source' => 'quran',
                ],
                [
                    'title' => 'His Son Refused to Board',
                    'description' => "Nuh called his son to the ark, but he chose to trust a mountain instead of Allah.",
                    'period' => 'Sorrow',
                    'order' => 6,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Ark Rests on Judiyy',
                    'description' => 'The waters subsided and the ship came to rest on Mount Judiyy.',
                    'period' => 'Relief',
                    'order' => 7,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Humble Correction',
                    'description' => "Nuh sought Allah's forgiveness for asking about something beyond his knowledge.",
                    'period' => 'Humility',
                    'order' => 8,
                    'source' => 'quran',
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
                    'source' => 'quran',
                ],
                [
                    'title' => 'Breaking the Idols',
                    'description' => "He shattered his people's idols, exposing the absurdity of worshipping what cannot speak.",
                    'period' => 'Confrontation',
                    'order' => 2,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Thrown into the Fire',
                    'description' => 'Sentenced to burn for his defiance, Allah commanded the fire to be coolness and safety upon him.',
                    'period' => 'Miracle',
                    'order' => 3,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Seeking Certainty',
                    'description' => 'He debated a tyrant king about life and death, and asked Allah to show him how the dead are revived.',
                    'period' => 'Deepening Faith',
                    'order' => 4,
                    'source' => 'quran',
                ],
                [
                    'title' => "Settling His Family Near the Sacred House",
                    'description' => "He left his descendants in a barren valley near the Ka'bah, trusting Allah completely for their provision.",
                    'period' => 'Trust',
                    'order' => 5,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Great Sacrifice',
                    'description' => "Commanded in a vision to sacrifice his son, both father and son submitted fully — until Allah ransomed him. The Quran does not name the son, though tradition holds it was Ismail (AS).",
                    'period' => 'The Trial',
                    'order' => 6,
                    'source' => 'quran', // trial itself is Quranic (37:102-107); son's identity is traditional, already disclosed in text
                ],
                [
                    'title' => "Building the Ka'bah",
                    'description' => 'Ibrahim and Ismail raised the foundations of the House of Allah and prayed for their descendants.',
                    'period' => 'Legacy',
                    'order' => 7,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Khalilullah — Friend of Allah',
                    'description' => 'His complete submission earned him a title unlike any other prophet: the intimate Friend of Allah.',
                    'period' => 'Honor',
                    'order' => 8,
                    'source' => 'quran',
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
                    'description' => "Musa was born as Pharaoh slaughtered the sons of the Israelites, and his mother set him afloat on the river in trust of Allah.",
                    'period' => 'Birth',
                    'order' => 1,
                    'source' => 'quran',
                ],
                [
                    'title' => "Raised in Pharaoh's House",
                    'description' => "Allah returned Musa to his mother's care, and he grew up within the very household of his people's oppressor.",
                    'period' => 'Upbringing',
                    'order' => 2,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Flight to Midian',
                    'description' => 'After a fatal altercation, Musa fled Egypt in fear, and Allah guided him safely to Midian.',
                    'period' => 'Exile',
                    'order' => 3,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Call at Mount Tur',
                    'description' => 'Allah spoke directly to Musa from the sacred valley, granting him prophethood and the staff and hand as signs.',
                    'period' => 'Prophethood',
                    'order' => 4,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Confronting Pharaoh',
                    'description' => "Musa and Harun delivered Allah's message to Pharaoh, calling him to release the Israelites and worship the One God.",
                    'period' => 'Confrontation',
                    'order' => 5,
                    'source' => 'quran',
                ],
                [
                    'title' => "The Magicians' Contest",
                    'description' => "Pharaoh's sorcerers were defeated when Allah caused Musa's staff to overcome their illusions, and the magicians believed.",
                    'period' => 'Sign',
                    'order' => 6,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Exodus and the Red Sea',
                    'description' => 'Allah split the sea for Musa and the believers to cross, and drowned Pharaoh and his army as they pursued.',
                    'period' => 'Deliverance',
                    'order' => 7,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Trials in the Wilderness',
                    'description' => "Despite Allah's provision of manna and quails, the people complained, and among them Qarun's arrogance led to his destruction.",
                    'period' => 'Testing',
                    'order' => 8,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Journey with a Righteous Servant',
                    'description' => "Musa sought knowledge from a righteous servant of Allah (named Khidr in Hadith, unnamed in the Quran), learning patience through three events whose true wisdom was hidden from him.",
                    'period' => 'Humility',
                    'order' => 9,
                    'source' => 'quran', // story is Quranic (18:60-82); the name "Khidr" comes from Sahih al-Bukhari
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
                    'source' => 'quran',
                ],
                [
                    'title' => 'Thrown into the Well',
                    'description' => 'His jealous brothers cast him into a well, abandoning him to despair.',
                    'period' => 'Betrayal',
                    'order' => 2,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Sold into Slavery',
                    'description' => 'A caravan found him and sold him as a slave in Egypt for a small price.',
                    'period' => 'Captivity',
                    'order' => 3,
                    'source' => 'quran',
                ],
                [
                    'title' => 'In the House of al-Azeez',
                    'description' => 'Yusuf served the Egyptian official with honesty and excellence, earning complete trust.',
                    'period' => 'Faithfulness',
                    'order' => 4,
                    'source' => 'quran',
                ],
                [
                    'title' => 'False Accusation',
                    'description' => 'The wife of al-Azeez falsely accused him of seduction when he refused her advances.',
                    'period' => 'Trial of Innocence',
                    'order' => 5,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Years in Prison',
                    'description' => 'Imprisoned for a crime he did not commit, Yusuf remained patient and steadfast in faith.',
                    'period' => 'Patience',
                    'order' => 6,
                    'source' => 'quran',
                ],
                [
                    'title' => "The King's Dream Interpreted",
                    'description' => 'Released from prison to interpret the king\'s dream of seven fat and seven lean cows.',
                    'period' => 'Recognition',
                    'order' => 7,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Appointed Over the Treasures',
                    'description' => "The king honored him and placed him in charge of Egypt's storehouses.",
                    'period' => 'Authority',
                    'order' => 8,
                    'source' => 'quran',
                ],
                [
                    'title' => 'His Brothers Arrive in Egypt',
                    'description' => 'During the famine, his brothers came to buy grain, not recognizing him.',
                    'period' => 'Recognition Deferred',
                    'order' => 9,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Planted Cup Test',
                    'description' => "He tested his brothers by secretly placing a golden cup in Benjamin's saddlebag.",
                    'period' => 'Divine Trial',
                    'order' => 10,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Benjamin Held in Egypt',
                    'description' => 'His youngest brother became his companion while the others despaired.',
                    'period' => 'Sorrow and Hope',
                    'order' => 11,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Yusuf Reveals Himself',
                    'description' => 'He declared his identity to his brothers, revealing he was their long-lost brother.',
                    'period' => 'The Reveal',
                    'order' => 12,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Complete Forgiveness',
                    'description' => 'He forgave his brothers entirely, telling them no blame would be held against them.',
                    'period' => 'Mercy',
                    'order' => 13,
                    'source' => 'quran',
                ],
                [
                    'title' => "Yaqub's Sight Restored",
                    'description' => "His father's eyes, white with grief for decades, regained sight when Yusuf's shirt touched them.",
                    'period' => 'Divine Healing',
                    'order' => 14,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Family Reunited in Egypt',
                    'description' => 'His entire family — parents, brothers, and their households — came to live under his care.',
                    'period' => 'Reunion',
                    'order' => 15,
                    'source' => 'quran',
                ],
                [
                    'title' => 'The Dream Fulfilled',
                    'description' => "His parents and siblings bowed before him, exactly as his dream from childhood had foretold.",
                    'period' => 'Divine Promise Realized',
                    'order' => 16,
                    'source' => 'quran',
                ],
                [
                    'title' => 'Dua for the Hereafter',
                    'description' => 'At the height of his worldly honor, he prayed to die as a Muslim and be joined with the righteous.',
                    'period' => 'Spiritual Completion',
                    'order' => 17,
                    'source' => 'quran',
                ],
            ],
        ]);

        $this->command->info('  ✅ Yusuf timeline seeded.');
    }

    private function seedIdris(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'idris'],
            [
                'timeline' => [
                    [
                        'title' => 'A Man of Truth and Prophethood',
                        'description' => 'Allah described Idris as a truthful man and a prophet.',
                        'period' => 'Truthfulness',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Raised to a High Station',
                        'description' => 'Allah raised Idris to a high and honored station.',
                        'period' => 'Honor',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Idris timeline seeded.');
    }

    private function seedHud(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'hud'],
            [
                'timeline' => [
                    [
                        'title' => 'His Call to Worship Allah Alone',
                        'description' => "Hud called his people, the 'Ad, to leave idols and worship Allah alone.",
                        'period' => 'Call',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "The People's Arrogance and Rejection",
                        'description' => "The 'Ad were proud of their strength and rejected Hud's warning.",
                        'period' => 'Rejection',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "The Destruction of 'Ad and Hud's Rescue",
                        'description' => "Allah destroyed the 'Ad and saved Hud and the believers with him.",
                        'period' => 'Punishment',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'A Punishing Wind as a Sign',
                        'description' => 'A fierce, relentless wind struck the people as a lasting sign for those who reflect.',
                        'period' => 'Warning',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Hud timeline seeded.');
    }

    private function seedSalih(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'salih'],
            [
                'timeline' => [
                    [
                        'title' => 'The Call to Thamud',
                        'description' => 'Salih called his people, Thamud, to worship Allah alone and leave their idols.',
                        'period' => 'Call',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Sign of the She-Camel',
                        'description' => 'Allah sent a she-camel as a clear sign, with a warning not to harm her.',
                        'period' => 'Divine Sign',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "The Elite's Arrogant Rejection",
                        'description' => 'While some believed, the proud leaders of Thamud mocked and rejected Salih.',
                        'period' => 'Rejection',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'A Plot to Kill Salih',
                        'description' => 'A group of men conspired secretly to kill Salih and silence his message.',
                        'period' => 'Conspiracy',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The She-Camel Killed',
                        'description' => "Thamud broke their promise and hamstrung the she-camel, defying Allah's warning.",
                        'period' => 'Defiance',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Three Days to Repent',
                        'description' => 'Salih warned them they had three days before the punishment would arrive.',
                        'period' => 'Final Warning',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Destruction of Thamud',
                        'description' => 'A mighty blast overtook them, and by morning they lay lifeless in their homes.',
                        'period' => 'Punishment',
                        'order' => 7,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "Salih's Sorrow for His People",
                        'description' => 'Salih turned away from them in grief, having delivered the truth they refused to accept.',
                        'period' => 'Aftermath',
                        'order' => 8,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Salih timeline seeded.');
    }

    private function seedHarun(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'harun'],
            [
                'timeline' => [
                    [
                        'title' => 'Born as the Brother of Musa',
                        'description' => "Harun was born as Musa's brother (traditionally held to be the elder of the two), later chosen by Allah to support him in his mission.",
                        'period' => 'Early Life',
                        'order' => 1,
                        'source' => 'tradition', // brotherhood is Quranic; "older" age-order detail is from tafsir
                    ],
                    [
                        'title' => 'Given as a Prophet, Out of Mercy',
                        'description' => "Allah granted Harun to Musa as a prophet, a gift of mercy to strengthen and support his brother's mission.",
                        'period' => 'Appointed a Prophet',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Musa Asks for Harun as His Helper',
                        'description' => "Before facing Fir'awn, Musa asked Allah to appoint Harun as his helper, to increase his strength and share his task.",
                        'period' => 'Mission Begins',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Stands Before Fir\'awn',
                        'description' => "Harun stood with Musa in front of Fir'awn, delivering the message of Allah and calling for the release of Bani Israel.",
                        'period' => 'Facing Fir\'awn',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Magicians Believe',
                        'description' => "Harun witnessed the royal magicians fall into prostration, believing in the Lord of Musa and Harun after seeing Allah's sign.",
                        'period' => 'A Turning Point',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Left in Charge of Bani Israel',
                        'description' => 'While Musa went to receive revelation from Allah, Harun was left responsible for guiding their people.',
                        'period' => 'Trial Begins',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Warns Against the Golden Calf',
                        'description' => 'Harun warned his people that the golden calf was only a test, urging them to follow him and obey, but they refused to listen.',
                        'period' => 'Rejection',
                        'order' => 7,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Explains His Actions to Musa',
                        'description' => 'When Musa returned upset, Harun calmly explained that he feared causing division among Bani Israel by using force.',
                        'period' => 'Wisdom Under Pressure',
                        'order' => 8,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Harun timeline seeded.');
    }

    private function seedLut(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'lut'],
            [
                'timeline' => [
                    [
                        'title' => 'Sent as a Warner',
                        'description' => 'Lut was sent to a people who had fallen into an immorality no nation before them had ever committed.',
                        'period' => 'Prophetic Mission',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Rejection by His People',
                        'description' => 'Despite his clear warnings, his people mocked him and threatened to evict him from the city.',
                        'period' => 'Defiance',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Angels Arrive as Guests',
                        'description' => 'Angels came to Lut in human form, causing him deep anguish as he feared for their safety among his people.',
                        'period' => 'Rising Danger',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Mob at His Door',
                        'description' => 'His people rushed toward his house with evil intent, and Lut tried desperately to protect his guests.',
                        'period' => 'Crisis',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Angels Reveal Themselves',
                        'description' => 'The guests revealed they were messengers of Allah and instructed Lut to leave with his family during the night.',
                        'period' => 'Divine Intervention',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "His Wife Left Behind",
                        'description' => "Lut's wife had secretly sided with the disbelievers, and so she was not saved along with the rest of the family.",
                        'period' => 'Consequence of Betrayal',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The City Destroyed',
                        'description' => "At dawn, the city was overturned and struck with a rain of stones, ending the people's persistent defiance.",
                        'period' => 'Divine Punishment',
                        'order' => 7,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'A Sign for Those Who Reason',
                        'description' => 'Allah left the ruins as a clear and lasting sign for people who reflect and use their reason.',
                        'period' => 'Eternal Lesson',
                        'order' => 8,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Lut timeline seeded.');
    }

    private function seedZakariyya(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'zakariyya'],
            [
                'timeline' => [
                    [
                        'title' => 'A Private Prayer for a Son',
                        'description' => 'Zakariyya, old and without children, quietly asked Allah for an heir who would be righteous and carry on his mission.',
                        'period' => 'The Prayer',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Good News of Yahya',
                        'description' => 'Allah gave Zakariyya the good news of a son named Yahya, a name never given to anyone before him.',
                        'period' => 'The Answer',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "Zakariyya's Wonder and the Sign",
                        'description' => 'Zakariyya wondered how this could happen given his old age, and was given a sign: days of silence while otherwise sound and healthy.',
                        'period' => 'The Answer',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Angels Deliver the News in Prayer',
                        'description' => 'While standing in prayer, angels spoke directly to Zakariyya, confirming the good news of Yahya as a righteous and honorable prophet.',
                        'period' => 'The Answer',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Zakariyya timeline seeded.');
    }

    private function seedYahya(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'yahya'],
            [
                'timeline' => [
                    [
                        'title' => 'Good News to Zakariyya',
                        'description' => 'Allah gives Zakariyya (AS) the good news of a son named Yahya — a name given to no one before him.',
                        'period' => 'The Announcement',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Sign of Silence',
                        'description' => 'Zakariyya (AS) asks for a sign, and is told he will not speak to people for three nights, even while being healthy.',
                        'period' => 'The Sign',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Birth and Wisdom',
                        'description' => 'Yahya (AS) is born and given wisdom and understanding of the Scripture while still a child.',
                        'period' => 'A Special Childhood',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'A Life of Purity and Devotion',
                        'description' => 'Yahya (AS) grows up pure, God-fearing, and devoted to his parents, never disobedient or arrogant.',
                        'period' => 'His Character',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Peace in Every Stage of Life',
                        'description' => 'Allah grants Yahya (AS) peace on the day he was born, the day he dies, and the day he will be raised again.',
                        'period' => 'The Promise of Peace',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Yahya timeline seeded.');
    }
    private function seedIsmail(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'ismail'],
            [
                'timeline' => [
                    [
                        'title' => 'Left in the Desert',
                        'description' => "Ibrahim left baby Ismail and his mother (named Hajar in Hadith) in the barren valley of Makkah, trusting fully in Allah.",
                        'period' => 'Trial of Trust',
                        'order' => 1,
                        'source' => 'hadith', // settling in the valley is Quranic (14:37); the name "Hajar" is from Sahih al-Bukhari
                    ],
                    [
                        'title' => 'The Spring of Zamzam',
                        'description' => "As his mother searched desperately for water, the blessed spring of Zamzam gushed forth beneath the infant's feet — an event recorded in authentic hadith.",
                        'period' => 'Divine Mercy (Hadith)',
                        'order' => 2,
                        'source' => 'hadith',
                    ],
                    [
                        'title' => 'Growing with Patience and Character',
                        'description' => 'Ismail grew into a young man known for his truthfulness, obedience, and patient character.',
                        'period' => 'Righteous Character',
                        'order' => 3,
                        'source' => 'quran', // 19:54-55
                    ],
                    [
                        'title' => 'The Dream of Sacrifice',
                        'description' => "Ibrahim saw a vision commanding him to sacrifice his son, and shared this trial with him directly. The Quran does not name the son, though tradition holds it was Ismail (AS).",
                        'period' => 'Divine Trial',
                        'order' => 4,
                        'source' => 'tradition', // trial is Quranic (37:102), son's identity as Ismail is traditional
                    ],
                    [
                        'title' => 'Willing Submission',
                        'description' => 'The son responded with complete submission, telling his father to carry out what Allah had commanded, patiently and without protest.',
                        'period' => 'Submission',
                        'order' => 5,
                        'source' => 'tradition', // same identity caveat as above
                    ],
                    [
                        'title' => 'The Ransom',
                        'description' => 'At the moment of sacrifice, Allah replaced the son with a ram, honoring both father and son for their obedience.',
                        'period' => 'Divine Reward',
                        'order' => 6,
                        'source' => 'tradition', // same identity caveat as above
                    ],
                    [
                        'title' => "Building the Ka'bah",
                        'description' => "Ismail joined his father Ibrahim in raising the foundations of the Ka'bah, the first house of worship built for Allah.",
                        'period' => 'Sacred Legacy',
                        'order' => 7,
                        'source' => 'quran', // 2:127, Ismail named explicitly here
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Ismail timeline seeded.');
    }

    private function seedIshaq(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'ishaq'],
            [
                'timeline' => [
                    [
                        'title' => 'Glad Tidings to Ibrahim',
                        'description' => 'Angels visiting Ibrahim in the form of guests gave him the good news of a son to be born, Ishaq, and after him, a grandson, Yaqub.',
                        'period' => 'Divine Promise',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "His Wife's Astonishment",
                        'description' => "Ibrahim's wife (named Sarah in tradition), old in age, expressed amazement at the news, and the angels reminded her that nothing is impossible for Allah's command.",
                        'period' => 'Wonder and Reassurance',
                        'order' => 2,
                        'source' => 'tradition', // event is Quranic (11:72-73); the name "Sarah" is not in the Quran text
                    ],
                    [
                        'title' => 'Birth of the Promised Son',
                        'description' => 'Ishaq was born to Ibrahim and his wife in their old age, a living sign of Allah\'s power and mercy.',
                        'period' => 'Fulfillment',
                        'order' => 3,
                        'source' => 'quran', // 37:112
                    ],
                    [
                        'title' => 'Made a Righteous Prophet',
                        'description' => 'Allah made Ishaq righteous and later a prophet, continuing the line of guidance from his father Ibrahim.',
                        'period' => 'Prophethood',
                        'order' => 4,
                        'source' => 'quran', // 19:49, 21:72
                    ],
                    [
                        'title' => 'A Blessed Legacy',
                        'description' => 'Ishaq and his descendants, including Yaqub, were made leaders guiding by Allah\'s command, carrying forward the family of prophethood.',
                        'period' => 'Lasting Legacy',
                        'order' => 5,
                        'source' => 'quran', // 21:72-73
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Ishaq timeline seeded.');
    }

    private function seedYaqub(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'yaqub'],
            [
                'timeline' => [
                    [
                        'title' => 'Warns Yusuf About His Dream',
                        'description' => "When young Yusuf shared his dream of eleven stars, the sun, and the moon bowing to him, Yaqub wisely told him to keep it secret, fearing his brothers' jealousy.",
                        'period' => 'Wisdom',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Sons Ask to Take Yusuf Out',
                        'description' => 'Yusuf\'s brothers convinced Yaqub to let Yusuf go out with them, promising to protect him.',
                        'period' => 'Trust Tested',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The False Blood-Stained Shirt',
                        'description' => 'The brothers returned at night weeping, claiming a wolf ate Yusuf, and showed Yaqub his shirt stained with false blood. Yaqub sensed the truth and chose beautiful patience.',
                        'period' => 'Beautiful Patience',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Years of Grief and Blindness',
                        'description' => "Yaqub's grief for Yusuf lasted so long that his eyes turned white from constant weeping, yet he never complained to people — only to Allah.",
                        'period' => 'Enduring Sorrow',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Never Losing Hope in Allah',
                        'description' => "Even after many years, Yaqub told his sons to keep searching for Yusuf, reminding them that only disbelievers lose hope in Allah's mercy.",
                        'period' => 'Unshaken Hope',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Senses the Scent of Yusuf',
                        'description' => 'Before any news reached him, Yaqub told those around him he could sense the scent of Yusuf, though they thought his old grief had confused him.',
                        'period' => 'Divine Connection',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "Sight Restored by Yusuf's Shirt",
                        'description' => "When Yusuf's shirt was placed over his face, Yaqub's eyesight returned instantly, exactly as Yusuf had promised.",
                        'period' => 'Divine Healing',
                        'order' => 7,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Forgives His Sons',
                        'description' => "When his sons admitted their wrongdoing and asked forgiveness, Yaqub promised to ask Allah's forgiveness for them.",
                        'period' => 'Mercy',
                        'order' => 8,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Reunited in Egypt',
                        'description' => 'Yaqub and his family were reunited with Yusuf in Egypt, and his parents bowed before him — fulfilling the dream from decades earlier.',
                        'period' => 'Reunion',
                        'order' => 9,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Final Advice to His Sons',
                        'description' => 'On his deathbed, Yaqub asked his sons what they would worship after him. They affirmed belief in the one God of Ibrahim, Ismail, and Ishaq, showing his lifelong teaching had succeeded.',
                        'period' => 'Legacy of Faith',
                        'order' => 10,
                        'source' => 'quran', // 2:133
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Yaqub timeline seeded.');
    }

    private function seedAyyub(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'ayyub'],
            [
                'timeline' => [
                    [
                        'title' => 'Touched by Hardship',
                        'description' => "Ayyub, called 'Our servant' by Allah, was touched by severe hardship and adversity.",
                        'period' => 'Trial',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Calls Upon His Lord',
                        'description' => 'He called to Allah, saying adversity had touched him, and affirming that Allah is the Most Merciful of the merciful.',
                        'period' => 'Turning to Allah',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Commanded to Strike the Ground',
                        'description' => 'Allah told him to strike the ground with his foot, and a cool spring of water appeared for him to bathe and drink from.',
                        'period' => 'Divine Response',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Hardship Removed',
                        'description' => 'Allah removed all his hardship and answered his call, as a mercy from Him.',
                        'period' => 'Healing',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Family Restored, and More',
                        'description' => 'Allah gave him back his family, and the like of them again with them, as mercy from Him and a reminder for worshippers.',
                        'period' => 'Restoration',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Kept His Oath Without Harm',
                        'description' => 'Allah showed him a way to fulfill a promise he had made, without breaking his oath or causing harm.',
                        'period' => 'Mercy in Detail',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Praised as a Patient, Excellent Servant',
                        'description' => 'Allah described him as patient, an excellent servant, and one who repeatedly turned back to Him.',
                        'period' => 'Highest Praise',
                        'order' => 7,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Named Among the Great Prophets',
                        'description' => 'Allah mentioned Ayyub directly alongside Nuh, Ibrahim, Ismail, Ishaq, Yaqub, Isa, Yunus, Harun, Sulayman, and Dawud, as recipients of revelation.',
                        'period' => 'Honored Legacy',
                        'order' => 8,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Ayyub timeline seeded.');
    }

    private function seedDhulKifl(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'dhul-kifl'],
            [
                'timeline' => [
                    [
                        'title' => 'Mentioned Among the Patient',
                        'description' => 'Allah mentions Dhul-Kifl alongside Ismail and Idris, describing all three as patient.',
                        'period' => 'Remembered for Patience',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "Admitted Into Allah's Mercy",
                        'description' => 'Dhul-Kifl was admitted into Allah\'s mercy for being among the righteous.',
                        'period' => 'Divine Mercy',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Counted Among the Outstanding',
                        'description' => "In Surah Sad, Dhul-Kifl is again mentioned alongside Ismail and Al-Yasa', described as among the outstanding.",
                        'period' => 'Remembered for Righteousness',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Dhul-Kifl timeline seeded.');
    }

    private function seedDawud(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'dawud'],
            [
                'timeline' => [
                    [
                        'title' => 'Young Warrior in the Army of Talut',
                        'description' => 'Dawud fought as a young soldier against the forces of Jalut (Goliath), showing early courage before his prophethood.',
                        'period' => 'Early Life',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Defeats Jalut and Given Kingship',
                        'description' => 'Dawud killed Jalut in battle, and Allah gave him kingship, wisdom, and knowledge as a result.',
                        'period' => 'Rise to Kingship',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Blessed with a Voice That Moved Mountains',
                        'description' => 'Allah gave Dawud a special bounty — mountains and birds would glorify Allah together with him.',
                        'period' => 'Divine Gifts',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Given the Skill of Making Armor',
                        'description' => 'Allah softened iron for Dawud and taught him to craft coats of armor to protect people in battle.',
                        'period' => 'Divine Gifts',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Strengthened Kingdom and Wisdom',
                        'description' => "Allah strengthened Dawud's rule and gave him wisdom and clarity in judgment and speech.",
                        'period' => 'Prophet-King',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Tested by Two Disputants',
                        'description' => 'Two men climbed into his prayer chamber to test his judgment with a dispute about ewes, leading Dawud to realize it was a trial from Allah.',
                        'period' => 'A Test of Judgment',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Turns Back to Allah in Repentance',
                        'description' => 'Realizing the encounter was a test, Dawud asked forgiveness from Allah and fell down in repentance, and Allah forgave him.',
                        'period' => 'Repentance',
                        'order' => 7,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Appointed as a Khalifah on Earth',
                        'description' => 'Allah told Dawud he was made a steward on earth, commanding him to judge between people with truth and not follow desires.',
                        'period' => 'Responsibility',
                        'order' => 8,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Judges the Case of the Field',
                        'description' => 'Dawud and his son Sulayman judged a case about sheep that had overrun a man\'s field at night, with Sulayman given deeper understanding of it.',
                        'period' => 'Shared Wisdom',
                        'order' => 9,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Grateful for the Gift of Knowledge',
                        'description' => 'Dawud and Sulayman both praised Allah for the knowledge He gave them, recognizing it as a favor above many of His servants.',
                        'period' => 'Gratitude',
                        'order' => 10,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Dawud timeline seeded.');
    }

    private function seedSulayman(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'sulayman'],
            [
                'timeline' => [
                    [
                        'title' => 'Given Knowledge and Inherits Kingship',
                        'description' => 'Sulayman inherited prophethood and kingship from his father Dawud, and Allah taught him the language of birds and gave him command over an army of jinn, men, and birds.',
                        'period' => 'Mission Begins',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Valley of the Ants',
                        'description' => 'While marching with his army, Sulayman heard an ant warn others to take shelter, and he smiled and thanked Allah for the gift of understanding her speech.',
                        'period' => 'Mission Begins',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "The Hoopoe's Report on Sheba",
                        'description' => 'The hoopoe bird returned with news of Queen Bilqis, who ruled a great kingdom but worshipped the sun instead of Allah.',
                        'period' => 'Call to Bilqis',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "The Letter and Bilqis's Response",
                        'description' => "Sulayman sent a letter calling Bilqis and her people to submit to Allah. She consulted her chiefs and sent a gift to test his intentions, which he firmly rejected.",
                        'period' => 'Call to Bilqis',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Throne Brought Instantly',
                        'description' => "By Allah's permission, a man with knowledge of the Scripture brought Bilqis's throne to Sulayman in an instant, a sign of Allah's favor.",
                        'period' => 'Call to Bilqis',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Bilqis Submits to Allah',
                        'description' => "Upon recognizing her disguised throne and entering Sulayman's glass palace, Bilqis acknowledged her past wrongdoing and submitted to Allah, Lord of the worlds.",
                        'period' => 'Call to Bilqis',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Test of the Racehorses',
                        'description' => 'Absorbed in admiring fine horses, Sulayman let the time of remembrance pass, then immediately turned back to Allah upon realizing it.',
                        'period' => 'Trial and Repentance',
                        'order' => 7,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Trial on His Throne',
                        'description' => 'Allah tested Sulayman further and he responded by seeking forgiveness and asking for a kingdom unmatched by anyone after him.',
                        'period' => 'Trial and Repentance',
                        'order' => 8,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Command Over Wind and Jinn Labor',
                        'description' => 'Allah subjected the wind to Sulayman\'s service and had jinn build grand structures for him, calling his family to gratitude.',
                        'period' => 'Divine Mercy',
                        'order' => 9,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Death of Sulayman',
                        'description' => 'Sulayman died standing, leaning on his staff, and his death went unnoticed by the jinn until a termite ate through the staff, showing they had no knowledge of the unseen.',
                        'period' => 'Divine Mercy',
                        'order' => 10,
                        'source' => 'quran', // termite/staff detail is explicit in 34:14
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Sulayman timeline seeded.');
    }

    private function seedIlyas(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'ilyas'],
            [
                'timeline' => [
                    [
                        'title' => 'Sent as a Messenger',
                        'description' => 'Ilyas was sent by Allah as a messenger to his people, calling them to fear Allah alone.',
                        'period' => 'Mission Begins',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "Calling People Away from Ba'l",
                        'description' => "Ilyas confronted his people for worshipping the idol Ba'l instead of Allah, reminding them Allah is the true Lord of their forefathers.",
                        'period' => 'Mission Begins',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'His People Reject Him',
                        'description' => 'His people denied him and refused to give up idol worship, except for a small group of sincere believers who were saved.',
                        'period' => 'Rejection Begins',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Honored for Generations',
                        'description' => 'Allah left good mention of Ilyas among later generations and greeted him with peace, rewarding him for his patience and sincerity.',
                        'period' => 'Divine Mercy',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Ilyas timeline seeded.');
    }

    private function seedAlYasa(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'al-yasa'],
            [
                'timeline' => [
                    [
                        'title' => 'Named Among the Preferred Prophets',
                        'description' => "Al-Yasa' is mentioned by Allah alongside Ismail, Yunus, and Lut, as one of those preferred over the worlds.",
                        'period' => 'Divine Mercy',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Named Among the Outstanding',
                        'description' => "Al-Yasa' is mentioned again alongside Ismail and Dhul-Kifl, described as among the outstanding servants of Allah.",
                        'period' => 'Divine Mercy',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Al-Yasa timeline seeded.');
    }

    private function seedShuayb(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'shuayb'],
            [
                'timeline' => [
                    [
                        'title' => 'Sent to the People of Madyan',
                        'description' => "Shu'ayb was sent as a messenger to his own people, the people of Madyan, calling them to worship Allah alone.",
                        'period' => 'Mission Begins',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Calls for Honest Trade',
                        'description' => 'He commanded his people to give full measure and weight in justice, and not to deprive people of their due in business.',
                        'period' => 'The Core Message',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Declares Himself a Trustworthy Messenger',
                        'description' => "Shu'ayb told his people he was a trustworthy messenger asking for no payment, proving his sincerity.",
                        'period' => 'Sincerity',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Threatened With Exile',
                        'description' => 'The arrogant leaders among his people threatened to expel him and the believers from their city, or force them back to their old religion.',
                        'period' => 'Rejection Begins',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Mocked and Called a Liar',
                        'description' => 'His people mocked his prayer, called him weak, and accused him of being affected by magic and lying.',
                        'period' => 'Mockery',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Warns Them of Earlier Nations',
                        'description' => "Shu'ayb reminded his people of what happened to the people of Nuh, Hud, Salih, and Lut, warning them not to be far behind.",
                        'period' => 'Final Warning',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Earthquake and the Shriek',
                        'description' => 'Those who rejected him were seized by an earthquake and a mighty shriek, and were wiped out completely, as if they had never lived there.',
                        'period' => 'Punishment',
                        'order' => 7,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "Shu'ayb and the Believers Saved",
                        'description' => 'Allah saved Shu\'ayb and those who believed with him, by mercy from Him, while the wrongdoers were destroyed.',
                        'period' => 'Divine Mercy',
                        'order' => 8,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Shuayb timeline seeded.');
    }

    private function seedIsa(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'isa'],
            [
                'timeline' => [
                    [
                        'title' => 'Good News to Maryam',
                        'description' => 'An angel comes to Maryam (AS) with news of a pure son, though no man had touched her.',
                        'period' => 'The Announcement',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Birth in a Remote Place',
                        'description' => 'Maryam (AS) gives birth alone in a remote place, and Allah provides comfort, water, and dates for her.',
                        'period' => 'The Birth',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Speaking from the Cradle',
                        'description' => 'When her people accuse her, baby Isa (AS) speaks from the cradle, declaring himself a servant and prophet of Allah.',
                        'period' => 'The Miracle Child',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Sent with Signs to Bani Israel',
                        'description' => "Isa (AS) grows up and is sent as a messenger, performing miracles by Allah's permission and calling people to worship Allah alone.",
                        'period' => 'The Mission',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Raised to Allah',
                        'description' => 'The people did not kill or crucify Isa (AS) — it only appeared so to them. Allah raised him to Himself.',
                        'period' => 'The Truth Revealed',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'His Testimony on Judgment Day',
                        'description' => 'The Quran clarifies Isa (AS) is a messenger and word from Allah, not divine — and he himself will testify he only called people to worship Allah alone.',
                        'period' => 'Setting the Record Straight',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Isa timeline seeded.');
    }

    private function seedYunus(): void
    {
        Prophet::updateOrCreate(
            ['slug' => 'yunus'],
            [
                'timeline' => [
                    [
                        'title' => 'Sent to His People',
                        'description' => 'Yunus was sent as a messenger to his people, calling them to worship Allah alone.',
                        'period' => 'Mission Begins',
                        'order' => 1,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Leaves in Anger',
                        'description' => 'Frustrated by his people\'s rejection, Yunus left without Allah\'s permission, thinking he would not be held to account.',
                        'period' => 'A Moment of Weakness',
                        'order' => 2,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Cast Into the Sea',
                        'description' => 'Boarding a heavily loaded ship, Yunus was thrown overboard after losing a drawing of lots.',
                        'period' => 'Trial',
                        'order' => 3,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Swallowed by the Whale',
                        'description' => 'A great fish swallowed Yunus by Allah\'s command, and he blamed himself for what had happened.',
                        'period' => 'Darkness Within Darkness',
                        'order' => 4,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'The Prayer in the Depths',
                        'description' => 'In the belly of the fish, Yunus called out that there is no god but Allah, glorifying Him and confessing his wrong.',
                        'period' => 'Turning to Allah',
                        'order' => 5,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Saved by Allah\'s Mercy',
                        'description' => 'Allah answered his call and saved him from his distress, as He saves the believers.',
                        'period' => 'Divine Rescue',
                        'order' => 6,
                        'source' => 'quran',
                    ],
                    [
                        'title' => 'Cast Onto the Shore',
                        'description' => 'Yunus was cast onto a barren shore in a weakened state, and Allah caused a gourd plant to grow over him for shade and comfort.',
                        'period' => 'Recovery',
                        'order' => 7,
                        'source' => 'quran',
                    ],
                    [
                        'title' => "His People Believe and Are Spared",
                        'description' => "Yunus was sent back to his people of over a hundred thousand, and this time they believed, so Allah lifted the punishment from them.",
                        'period' => 'A Rare Mercy',
                        'order' => 8,
                        'source' => 'quran', // Surah Yunus 10:98
                    ],
                ],
            ]
        );

        $this->command->info('  ✅ Yunus timeline seeded.');
    }
}
