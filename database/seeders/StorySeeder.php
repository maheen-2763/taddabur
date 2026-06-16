<?php
// database/seeders/StorySeeder.php

namespace Database\Seeders;

use App\Models\Prophet;
use App\Models\Story;
use App\Models\StoryChapter;
use Illuminate\Database\Seeder;

class StorySeeder extends Seeder
{
    public function run(): void
    {
        Story::truncate();
        StoryChapter::truncate();

        // Story of Adam (AS) — full example with chapters
        $adam = Prophet::where('slug', 'adam')->first();

        $adamStory = Story::create([
            'prophet_id'        => $adam->id,
            'title'             => 'The Story of Adam (AS) — The First Human',
            'slug'              => 'story-of-adam',
            'category'          => 'prophet',
            'summary'           => 'The story of the creation of the first human being, his life in Paradise, the trial with Iblis, and his descent to Earth — a story of honour, temptation, and mercy.',
            'difficulty'        => 'beginner',
            'is_free'           => true,     // Free plan users can read this
            'is_published'      => true,
            'sort_order'        => 1,
            'read_time_minutes' => 15,
            'quran_references'  => ['Al-Baqarah 2:30-39', 'Al-A\'raf 7:19-25', 'Ta-Ha 20:115-123'],
            'tags'              => ['creation', 'paradise', 'iblis', 'repentance', 'first human'],
        ]);

        // Chapters for Adam's story
        StoryChapter::create([
            'story_id' => $adamStory->id,
            'title'    => 'The Creation of Adam',
            'order'    => 1,
            'content'  => '<p>Before there was land or sea, before any human being walked the earth, Allah — the Creator of all things — decreed the creation of a new kind of being.</p>
            <p>Allah said to the angels: <em>"Indeed, I will place a successive authority upon the earth."</em> (Al-Baqarah 2:30)</p>
            <p>The angels, who knew only worship and obedience, wondered. They asked: "Will You place upon it one who causes corruption and sheds blood, while we declare Your praise and sanctify You?"</p>
            <p>Allah replied with the most profound of answers: <em>"Indeed, I know that which you do not know."</em></p>
            <p>Allah then created Adam from clay — from the earth itself. He fashioned him with His own hands, shaped him into the most beautiful form, and breathed into him from His spirit. In that moment, Adam (AS) became the first human being.</p>',
            'quran_references' => ['Al-Baqarah 2:30', 'Al-Hijr 15:28-29', 'As-Sajdah 32:7-9'],
        ]);

        StoryChapter::create([
            'story_id' => $adamStory->id,
            'title'    => 'The Names and the Honour of Adam',
            'order'    => 2,
            'content'  => '<p>Allah honoured Adam (AS) in a way no other creation had been honoured before. He taught him the names of all things — every creature, every object, every concept.</p>
            <p>Then Allah presented these things to the angels and said: <em>"Inform Me of the names of these, if you are truthful."</em> (Al-Baqarah 2:31)</p>
            <p>The angels, in their humility, replied: "Exalted are You; we have no knowledge except what You have taught us."</p>
            <p>Then Allah asked Adam (AS) — and Adam named them all. The angels immediately understood. This was the being Allah knew that they did not know. Adam possessed knowledge, reason, and the capacity to learn — gifts that made him worthy of the great responsibility ahead.</p>
            <p>Allah then commanded the angels: <em>"Prostrate to Adam."</em> And they all prostrated — in honour and recognition of this noble creation.</p>',
            'quran_references' => ['Al-Baqarah 2:31-34', 'Al-Isra 17:70'],
        ]);

        StoryChapter::create([
            'story_id' => $adamStory->id,
            'title'    => 'Iblis and the First Act of Arrogance',
            'order'    => 3,
            'content'  => '<p>All prostrated — except one.</p>
            <p>Iblis, who had been among the worshippers with the angels, refused. He stood alone in defiance.</p>
            <p>Allah asked: <em>"What prevented you from prostrating when I commanded you?"</em></p>
            <p>Iblis answered with the first act of arrogance in creation: <em>"I am better than him. You created me from fire and created him from clay."</em> (Al-A\'raf 7:12)</p>
            <p>In that moment, Iblis made a catastrophic error. He judged by origin and material rather than by the command and wisdom of Allah. He placed his own opinion above the command of his Creator.</p>
            <p>Allah said: <em>"Descend from Paradise. It is not for you to be arrogant here. Get out; indeed, you are of the debased."</em></p>
            <p>Iblis, now expelled, made a vow: he would mislead the children of Adam until the Day of Judgement. His enmity toward humanity had begun.</p>',
            'quran_references' => ['Al-A\'raf 7:11-18', 'Al-Baqarah 2:34', 'Al-Hijr 15:30-42'],
        ]);

        StoryChapter::create([
            'story_id' => $adamStory->id,
            'title'    => 'Paradise and the One Forbidden Tree',
            'order'    => 4,
            'content'  => '<p>Adam (AS) lived in Paradise — a place of unimaginable beauty and peace. Allah created Hawwa (Eve) as his companion, and together they lived in comfort and joy.</p>
            <p>Allah gave them complete freedom with one exception: <em>"Do not approach this tree, lest you be among the wrongdoers."</em> (Al-Baqarah 2:35)</p>
            <p>For a time, they lived in perfect obedience. But Iblis had not forgotten his vow. He whispered to them — slowly, persistently, craftily. He told them the tree would grant them eternal life. He swore by Allah that he was giving them sincere advice.</p>
            <p>Adam and Hawwa — not out of arrogance but out of human weakness — were deceived. They ate from the forbidden tree.</p>
            <p>Immediately, they felt something they had never felt before. They became aware of their vulnerability, their exposure. They began covering themselves with leaves from the garden. And a voice called to them: <em>"Did I not forbid you from that tree?"</em></p>
            <p>They did what Iblis had never done. They did not argue. They did not blame. They said: <em>"Our Lord, we have wronged ourselves, and if You do not forgive us and have mercy upon us, we will surely be among the losers."</em> (Al-A\'raf 7:23)</p>
            <p>In those words lies one of the greatest lessons of the Quran: the difference between Adam and Iblis was not that Adam never sinned — it is that Adam immediately returned to Allah.</p>',
            'quran_references' => ['Al-Baqarah 2:35-37', 'Al-A\'raf 7:19-23', 'Ta-Ha 20:120-121'],
        ]);

        StoryChapter::create([
            'story_id' => $adamStory->id,
            'title'    => 'The Descent and the Promise',
            'order'    => 5,
            'content'  => '<p>Allah accepted the repentance of Adam and Hawwa. He forgave them completely. But the test of this life was to begin.</p>
            <p>Allah said: <em>"Descend, all of you, from Paradise. And when guidance comes to you from Me, whoever follows My guidance — there will be no fear concerning them, nor will they grieve."</em> (Al-Baqarah 2:38)</p>
            <p>Adam and Hawwa descended to the earth. With them came a promise — the most important promise in human history: those who follow Allah\'s guidance will have nothing to fear.</p>
            <p>Adam (AS) became the first prophet — the first to carry Allah\'s message to his children. He taught them about their Creator, about worship, about right and wrong.</p>
            <p>The story of Adam teaches us four timeless truths:</p>
            <ul>
                <li><strong>Honour:</strong> Allah honoured the human being above all creation.</li>
                <li><strong>Test:</strong> Life on earth is a test — we will be tempted and we will sometimes fall.</li>
                <li><strong>Repentance:</strong> The door of forgiveness is always open to those who sincerely return to Allah.</li>
                <li><strong>The Enemy:</strong> Iblis is our declared enemy. His weapon is whisper; our shield is awareness and remembrance of Allah.</li>
            </ul>
            <p>Every child of Adam carries this story within them. And every time we sin and return to Allah in repentance, we follow in the footsteps of our father.</p>',
            'quran_references' => ['Al-Baqarah 2:38-39', 'Ta-Ha 20:122-123'],
        ]);

        // Story of Yunus (AS) — shorter example
        $yunus = Prophet::where('slug', 'yunus')->first();

        $yunusStory = Story::create([
            'prophet_id'        => $yunus->id,
            'title'             => 'The Story of Yunus (AS) — The Man of the Whale',
            'slug'              => 'story-of-yunus',
            'category'          => 'prophet',
            'summary'           => 'The powerful story of a prophet who left his people without permission, was swallowed by a whale, and called upon Allah from the depths of darkness — and was answered.',
            'difficulty'        => 'beginner',
            'is_free'           => true,
            'is_published'      => true,
            'sort_order'        => 2,
            'read_time_minutes' => 10,
            'quran_references'  => ['As-Saffat 37:139-148', 'Al-Anbiya 21:87-88', 'Yunus 10:98'],
            'tags'              => ['patience', 'dua', 'repentance', 'whale', 'forgiveness'],
        ]);

        StoryChapter::create([
            'story_id' => $yunusStory->id,
            'title'    => 'The Prophet Who Left Without Permission',
            'order'    => 1,
            'content'  => '<p>Yunus (AS) was sent to the city of Nineveh — a great city in ancient Iraq. He called his people to worship Allah alone. But they refused. They mocked him and turned away.</p>
            <p>Yunus (AS) grew frustrated. He left his people before Allah gave him permission to do so — an action that was not befitting of a prophet\'s station.</p>
            <p>He boarded a ship and set out to sea. But the sea would not let him pass in peace.</p>',
            'quran_references' => ['As-Saffat 37:139-142'],
        ]);

        StoryChapter::create([
            'story_id' => $yunusStory->id,
            'title'    => 'The Darkness Within Darkness',
            'order'    => 2,
            'content'  => '<p>A violent storm came upon the ship. The sailors, fearing the ship would sink, drew lots to decide who would be thrown overboard to lighten the weight. The lot fell on Yunus (AS) — three times.</p>
            <p>He was cast into the roaring sea. And there, by the command of Allah, a great whale swallowed him whole.</p>
            <p>Yunus (AS) found himself in a darkness like no human had ever experienced — the darkness of the whale\'s belly, in the darkness of the ocean depths, in the darkness of night. Three layers of darkness.</p>
            <p>But in that total darkness, he turned entirely to Allah. He called out with the most beautiful words of repentance ever spoken:</p>
            <blockquote><em>"Laa ilaaha illaa Anta, Subhaanaka, innee kuntu minaz-zaalimeen."</em><br>
            "There is no god but You. Glory be to You. Indeed, I have been of the wrongdoers." (Al-Anbiya 21:87)</em></blockquote>
            <p>No blame, no excuses. Just complete acknowledgement of his error and complete trust in Allah\'s mercy.</p>',
            'quran_references' => ['Al-Anbiya 21:87', 'As-Saffat 37:143-144'],
        ]);

        StoryChapter::create([
            'story_id' => $yunusStory->id,
            'title'    => 'The Answer and the Miracle',
            'order'    => 3,
            'content'  => '<p>Allah says in the Quran: <em>"And had he not been of those who exalt Allah, he would have remained inside its belly until the Day they are resurrected."</em> (As-Saffat 37:143-144)</p>
            <p>But Yunus (AS) was one who glorified Allah — and so Allah answered him.</p>
            <p><em>"So We responded to him and saved him from the distress. And thus do We save the believers."</em> (Al-Anbiya 21:88)</p>
            <p>The whale was commanded to release him. It cast him onto a shore, and Yunus (AS) emerged — weak and ill. Allah caused a gourd plant to grow over him to shade and nourish him as he recovered.</p>
            <p>And then — the miracle. His people in Nineveh, the very people who had rejected him, had seen something change in their hearts after his departure. They believed. An entire city turned to Allah in faith.</p>
            <p>Allah sent Yunus (AS) back to them — to over 100,000 people — and gave them provision for a time.</p>
            <p>The dua of Yunus (AS) — "La ilaha illa Anta, Subhanaka, inni kuntu minaz-zalimeen" — has been passed down through generations as one of the most powerful supplications. Our Prophet Muhammad (SAW) said: "No Muslim supplicates with it for anything, ever, except that Allah will respond to him."</p>',
            'quran_references' => ['As-Saffat 37:145-148', 'Al-Anbiya 21:88', 'Yunus 10:98'],
        ]);

        $this->command->info('✅ Stories seeded: 2 prophet stories with full chapters');
    }
}
