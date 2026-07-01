<?php

namespace Database\Seeders;

use App\Models\Scholar;
use App\Models\ScholarTeaching;
use App\Models\ScholarQuote;
use App\Models\ScholarStudent;
use App\Models\ScholarWork;
use Illuminate\Database\Seeder;

class ScholarSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // 1. IMAM ABU HANIFA (80–150 AH)
        // Source: Siyar A'lam al-Nubala, Al-Jawahir al-Mudiyyah
        // =============================================
        $abuHanifa = Scholar::create([
            'name'        => 'Imam Abu Hanifa',
            'arabic_name' => 'الإمام أبو حنيفة النعمان',
            'birth_ah'    => 80,
            'death_ah'    => 150,
            'madhab'      => 'hanafi',
            'slug'        => 'imam-abu-hanifa',
            'biography'   => 'Nu\'man ibn Thabit, known as Abu Hanifa, was born in Kufa (modern-day Iraq) in 80 AH. He was of Persian origin and grew up in a merchant family. He became one of the most influential jurists in Islamic history and founded the Hanafi school of thought, the most widely followed madhab in the world today. He studied under Hammad ibn Abi Sulayman for 18 years and met several Companions of the Prophet (Sahabah), thus being classified as a Tabi\'i.',
            'early_life'  => 'Abu Hanifa initially worked as a silk merchant in Kufa. His intellect was recognized by the scholar Al-Sha\'bi, who encouraged him to pursue Islamic knowledge. He memorized a vast number of hadiths and spent decades studying jurisprudence. He is reported to have completed the recitation of the Quran over 7,000 times in his lifetime.',
            'trials'      => 'Abu Hanifa was offered the position of Chief Judge (Qadi) by both the Umayyad and Abbasid caliphs, but he refused both times to maintain his independence. The Abbasid Caliph Al-Mansur imprisoned him for his refusal and he died in prison in 150 AH. He was also lashed for refusing the position.',
        ]);

        // Students
        ScholarStudent::insert([
            [
                'scholar_id'  => $abuHanifa->id,
                'name'        => 'Abu Yusuf (Ya\'qub ibn Ibrahim)',
                'arabic_name' => 'أبو يوسف يعقوب بن إبراهيم',
                'description' => 'Chief Justice under Caliph Harun al-Rashid. He compiled Abu Hanifa\'s legal opinions and wrote Kitab al-Kharaj.',
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $abuHanifa->id,
                'name'        => 'Muhammad ibn al-Hasan al-Shaybani',
                'arabic_name' => 'محمد بن الحسن الشيباني',
                'description' => 'Systematized and codified the Hanafi school. His books became the primary reference for Hanafi fiqh.',
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $abuHanifa->id,
                'name'        => 'Zufar ibn al-Hudhayl',
                'arabic_name' => 'زفر بن الهذيل',
                'description' => 'Known for his mastery of legal reasoning (qiyas) within the Hanafi school.',
                'created_at'  => now(),
                'updated_at' => now(),
            ],
        ]);

        // Works
        ScholarWork::insert([
            [
                'scholar_id'   => $abuHanifa->id,
                'title'        => 'Fiqh al-Akbar',
                'arabic_title' => 'الفقه الأكبر',
                'description'  => 'A foundational text on Islamic theology (aqeedah) dealing with matters of faith and creed.',
                'created_at'   => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'   => $abuHanifa->id,
                'title'        => 'Al-Musnad',
                'arabic_title' => 'المسند',
                'description'  => 'A collection of hadiths compiled by his student Abu Yusuf from Abu Hanifa\'s narrations.',
                'created_at'   => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'   => $abuHanifa->id,
                'title'        => 'Al-Fiqh al-Absat',
                'arabic_title' => 'الفقه الأبسط',
                'description'  => 'Another theological treatise addressing questions of Islamic belief.',
                'created_at'   => now(),
                'updated_at' => now(),
            ],
        ]);

        // Teachings
        ScholarTeaching::insert([
            [
                'scholar_id'  => $abuHanifa->id,
                'title'       => 'Use of Ra\'y (Personal Reasoning)',
                'content'     => 'Abu Hanifa was known for his extensive use of independent legal reasoning (ra\'y) when Quran and authentic Sunnah did not provide a direct ruling. He approached jurisprudence analytically, using rational deduction grounded in Islamic principles.',
                'order_index' => 1,
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $abuHanifa->id,
                'title'       => 'Qiyas (Analogical Reasoning)',
                'content'     => 'He strongly emphasized qiyas — deriving rulings by drawing analogy between a new issue and an existing ruling in the Quran and Sunnah based on a shared effective cause (illah).',
                'order_index' => 2,
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $abuHanifa->id,
                'title'       => 'Istihsan (Juristic Preference)',
                'content'     => 'Abu Hanifa introduced istihsan — choosing a ruling that departs from strict qiyas when applying it would lead to hardship or contradiction. This was a distinctive methodology of the Hanafi school.',
                'order_index' => 3,
                'created_at'  => now(),
                'updated_at' => now(),
            ],
        ]);

        // Quotes
        ScholarQuote::insert([
            [
                'scholar_id'    => $abuHanifa->id,
                'quote_arabic'  => 'إذا صحّ الحديث فهو مذهبي',
                'quote_english' => 'If the hadith is authentic, then that is my madhab.',
                'source'        => 'Ibn Abidin, Radd al-Muhtar; Al-Suyuti, Jazeel al-Mawahib',
                'created_at'    => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'    => $abuHanifa->id,
                'quote_arabic'  => 'العلم بلا عمل كالشجر بلا ثمر',
                'quote_english' => 'Knowledge without action is like a tree without fruit.',
                'source'        => 'Attributed in classical biographical sources',
                'created_at'    => now(),
                'updated_at' => now(),
            ],
        ]);


        // =============================================
        // 2. IMAM MALIK (93–179 AH)
        // Source: Tartib al-Madarik (Qadi Iyad), Siyar A'lam al-Nubala
        // =============================================
        $malik = Scholar::create([
            'name'        => 'Imam Malik',
            'arabic_name' => 'الإمام مالك بن أنس',
            'birth_ah'    => 93,
            'death_ah'    => 179,
            'madhab'      => 'maliki',
            'slug'        => 'imam-malik',
            'biography'   => 'Malik ibn Anas was born in Madinah in 93 AH and spent his entire life there. He studied under over 900 scholars and became the leading authority on the Sunnah of Madinah. He is the founder of the Maliki school of jurisprudence. Imam al-Shafi\'i studied under him and said: "Malik is the proof of God upon His creation after the Tabi\'in."',
            'early_life'  => 'Malik grew up in Madinah, the city of the Prophet ﷺ, surrounded by the living Sunnah. He studied under Nafi\' (the freed slave of Ibn Umar) and other great scholars. He memorized the Quran early and began issuing legal opinions (fatawa) at age 17 after being certified by 70 senior scholars.',
            'trials'      => 'In 148 AH, the Abbasid governor Ja\'far ibn Sulayman had Imam Malik flogged with 70 lashes for issuing a fatwa that oaths given under compulsion are not binding — a ruling that the governor feared could undermine forced pledges of allegiance. Imam Malik bore the punishment with patience and never recanted his position.',
        ]);

        ScholarStudent::insert([
            [
                'scholar_id'  => $malik->id,
                'name'        => 'Imam al-Shafi\'i',
                'arabic_name' => 'الإمام الشافعي',
                'description' => 'Later founded his own school of jurisprudence but considered Imam Malik his greatest teacher.',
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $malik->id,
                'name'        => 'Abd al-Rahman ibn al-Qasim',
                'arabic_name' => 'عبد الرحمن بن القاسم',
                'description' => 'One of Malik\'s closest and most trusted students. Transmitted the Muwatta and the school\'s legal opinions to Egypt.',
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $malik->id,
                'name'        => 'Yahya ibn Yahya al-Laythi',
                'arabic_name' => 'يحيى بن يحيى الليثي',
                'description' => 'Transmitted the Muwatta to Andalusia (Spain). The dominant Maliki recension in North Africa is through him.',
                'created_at'  => now(),
                'updated_at' => now(),
            ],
        ]);

        ScholarWork::insert([
            [
                'scholar_id'   => $malik->id,
                'title'        => 'Al-Muwatta',
                'arabic_title' => 'الموطأ',
                'description'  => 'The earliest surviving collection of hadith and fiqh. Imam al-Shafi\'i said: "No book on earth is more accurate than the Muwatta after the Book of Allah." Compiled over 40 years.',
                'created_at'   => now(),
                'updated_at' => now(),
            ],
        ]);

        ScholarTeaching::insert([
            [
                'scholar_id'  => $malik->id,
                'title'       => 'Amal Ahl al-Madinah (Practice of the People of Madinah)',
                'content'     => 'Imam Malik considered the consensus practice of the people of Madinah as a primary source of law, arguing that the community of Madinah had inherited the living Sunnah from the Prophet ﷺ directly through continuous practice (tawatur al-amal).',
                'order_index' => 1,
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $malik->id,
                'title'       => 'Maslaha Mursalah (Public Interest)',
                'content'     => 'Malik gave weight to the concept of unrestricted public interest (maslaha mursalah) — recognizing a benefit not explicitly stated in the texts when it aligns with the objectives of Islamic law (maqasid al-shari\'ah).',
                'order_index' => 2,
                'created_at'  => now(),
                'updated_at' => now(),
            ],
        ]);

        ScholarQuote::insert([
            [
                'scholar_id'    => $malik->id,
                'quote_arabic'  => 'لا يصلح آخر هذه الأمة إلا بما صلح به أولها',
                'quote_english' => 'The latter part of this Ummah cannot be rectified except by what rectified its earlier part.',
                'source'        => 'Tartib al-Madarik, Qadi Iyad',
                'created_at'    => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'    => $malik->id,
                'quote_arabic'  => 'كل أحد يؤخذ من قوله ويرد إلا صاحب هذا القبر ﷺ',
                'quote_english' => 'The opinion of every person can be accepted or rejected, except the one in this grave ﷺ (the Prophet).',
                'source'        => 'Ibn Abd al-Barr, Jami\' Bayan al-Ilm; widely transmitted',
                'created_at'    => now(),
                'updated_at' => now(),
            ],
        ]);


        // =============================================
        // 3. IMAM AL-SHAFI'I (150–204 AH)
        // Source: Manaqib al-Shafi'i (Al-Bayhaqi), Siyar A'lam al-Nubala
        // =============================================
        $shafii = Scholar::create([
            'name'        => "Imam al-Shafi'i",
            'arabic_name' => 'الإمام محمد بن إدريس الشافعي',
            'birth_ah'    => 150,
            'death_ah'    => 204,
            'madhab'      => 'shafi_i',
            'slug'        => 'imam-al-shafii',
            'biography'   => "Muhammad ibn Idris al-Shafi'i was born in Gaza in 150 AH, the same year Imam Abu Hanifa died. He was of Qurayshi descent, from the clan of Banu Muttalib — making him a distant relative of the Prophet ﷺ. He studied under Imam Malik in Madinah and later under Muhammad al-Shaybani (a student of Abu Hanifa) in Iraq. He founded the Shafi'i school and is credited with founding the science of usul al-fiqh (principles of Islamic jurisprudence).",
            'early_life'  => "Al-Shafi'i was orphaned at a young age and raised by his mother in Makkah. Despite poverty, he memorized the Quran by age 7 and al-Muwatta of Imam Malik by age 10. He spent years among the Hudhail tribe in the desert, mastering classical Arabic poetry and linguistics — knowledge that later profoundly shaped his understanding of the Quran and Sunnah.",
            'trials'      => "Al-Shafi'i was arrested in Yemen on charges of supporting an Alid rebellion and brought before the Abbasid Caliph Harun al-Rashid in chains. He defended himself eloquently and was released. He also faced intense scholarly criticism when he revised his earlier legal opinions (the 'Old School' vs his 'New School') after moving to Egypt.",
        ]);

        ScholarStudent::insert([
            [
                'scholar_id'  => $shafii->id,
                'name'        => 'Ahmad ibn Hanbal',
                'arabic_name' => 'أحمد بن حنبل',
                'description' => "Al-Shafi'i's most famous student, who later founded his own school. Al-Shafi'i said of him: 'I left Baghdad and no one there was more pious or more knowledgeable than Ahmad ibn Hanbal.'",
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $shafii->id,
                'name'        => 'Al-Muzani (Ismail ibn Yahya)',
                'arabic_name' => 'المزني إسماعيل بن يحيى',
                'description' => "One of al-Shafi'i's closest students in Egypt. Authored Mukhtasar al-Muzani, the primary reference text for the Shafi'i school.",
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $shafii->id,
                'name'        => 'Al-Rabi\' ibn Sulayman al-Muradi',
                'arabic_name' => 'الربيع بن سليمان المرادي',
                'description' => "Primary transmitter of al-Shafi'i's 'New School' opinions in Egypt. His narration of al-Umm is the most relied-upon.",
                'created_at'  => now(),
                'updated_at' => now(),
            ],
        ]);

        ScholarWork::insert([
            [
                'scholar_id'   => $shafii->id,
                'title'        => 'Al-Risalah',
                'arabic_title' => 'الرسالة',
                'description'  => "The first systematic work on Islamic legal theory (usul al-fiqh). Written at the request of Imam Abd al-Rahman ibn Mahdi. It defines the sources of Islamic law and their hierarchy.",
                'created_at'   => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'   => $shafii->id,
                'title'        => 'Al-Umm',
                'arabic_title' => 'الأم',
                'description'  => "A comprehensive encyclopedia of Shafi'i fiqh covering all major chapters of Islamic law, dictated by al-Shafi'i himself in Egypt — representing his 'New School' (al-madhhab al-jadid).",
                'created_at'   => now(),
                'updated_at' => now(),
            ],
        ]);

        ScholarTeaching::insert([
            [
                'scholar_id'  => $shafii->id,
                'title'       => 'Founding of Usul al-Fiqh',
                'content'     => "Al-Shafi'i systematized the four primary sources of Islamic law in a clear hierarchy: (1) Quran, (2) Sunnah, (3) Ijma (scholarly consensus), (4) Qiyas (analogical reasoning). His Al-Risalah established the methodology that all later schools of jurisprudence would engage with.",
                'order_index' => 1,
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $shafii->id,
                'title'       => "Old School vs New School (Qadim vs Jadid)",
                'content'     => "Al-Shafi'i revised many of his legal opinions after moving from Iraq to Egypt in 199 AH, forming what scholars call his 'New School' (al-jadid). The change resulted from access to new hadith narrators in Egypt and deeper reflection.",
                'order_index' => 2,
                'created_at'  => now(),
                'updated_at' => now(),
            ],
        ]);

        ScholarQuote::insert([
            [
                'scholar_id'    => $shafii->id,
                'quote_arabic'  => 'إذا وجدتم في كتابي خلاف سنة رسول الله ﷺ فقولوا بسنة رسول الله ﷺ ودعوا ما قلت',
                'quote_english' => 'If you find in my book something that contradicts the Sunnah of the Messenger of Allah ﷺ, then follow the Sunnah and leave what I said.',
                'source'        => 'Al-Nawawi, Al-Majmu; Ibn Abidin, Hashiyah',
                'created_at'    => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'    => $shafii->id,
                'quote_arabic'  => 'كلما ازددت علماً ازددت علماً بجهلي',
                'quote_english' => 'The more knowledge I gain, the more I realize my own ignorance.',
                'source'        => "Manaqib al-Shafi'i, Al-Bayhaqi",
                'created_at'    => now(),
                'updated_at' => now(),
            ],
        ]);


        // =============================================
        // 4. IMAM AHMAD IBN HANBAL (164–241 AH)
        // Source: Manaqib al-Imam Ahmad (Ibn al-Jawzi), Tabaqat al-Hanabilah
        // =============================================
        $ahmad = Scholar::create([
            'name'        => 'Imam Ahmad ibn Hanbal',
            'arabic_name' => 'الإمام أحمد بن محمد بن حنبل',
            'birth_ah'    => 164,
            'death_ah'    => 241,
            'madhab'      => 'hanbali',
            'slug'        => 'imam-ahmad-ibn-hanbal',
            'biography'   => "Ahmad ibn Muhammad ibn Hanbal was born in Baghdad in 164 AH. He is the founder of the Hanbali school and one of the greatest hadith scholars in Islamic history. He traveled extensively to collect hadith — to Kufa, Basra, Makkah, Madinah, Yemen, and Syria. He studied under Imam al-Shafi'i, who said of him: 'I left Baghdad and no one there was more pious or more knowledgeable in fiqh than Ahmad ibn Hanbal.' His Musnad contains over 27,000 hadith narrations.",
            'early_life'  => 'Ahmad was raised by his mother after his father died young. He began seeking Islamic knowledge at age 15 and studied under the greatest scholars of his era. He lived in extreme asceticism and is reported to have memorized over one million hadiths with their chains of narration (isnad).',
            'trials'      => "The Mihna (Inquisition) 218–234 AH: The Abbasid Caliphs Al-Ma'mun, Al-Mu'tasim, and Al-Wathiq adopted the Mu'tazilite doctrine that the Quran was 'created.' Ahmad ibn Hanbal refused to accept this position and maintained that the Quran is the eternal, uncreated Word of Allah. He was imprisoned, flogged, and tortured for over 28 months under Caliph Al-Mu'tasim. He never recanted. When the Mihna ended under Caliph Al-Mutawakkil (232 AH), Ahmad was released and celebrated across the Muslim world as a champion of the Sunnah.",
        ]);

        ScholarStudent::insert([
            [
                'scholar_id'  => $ahmad->id,
                'name'        => 'Al-Bukhari (Muhammad ibn Ismail)',
                'arabic_name' => 'البخاري محمد بن إسماعيل',
                'description' => 'Compiler of Sahih al-Bukhari, the most authentic hadith collection. He narrated from Ahmad ibn Hanbal.',
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $ahmad->id,
                'name'        => 'Muslim ibn al-Hajjaj',
                'arabic_name' => 'مسلم بن الحجاج',
                'description' => 'Compiler of Sahih Muslim. He studied hadith under Ahmad ibn Hanbal.',
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $ahmad->id,
                'name'        => 'Salih ibn Ahmad ibn Hanbal',
                'arabic_name' => 'صالح بن أحمد بن حنبل',
                'description' => "Ahmad's own son. Transmitted his father's legal opinions and wrote his biography.",
                'created_at'  => now(),
                'updated_at' => now(),
            ],
        ]);

        ScholarWork::insert([
            [
                'scholar_id'   => $ahmad->id,
                'title'        => 'Al-Musnad',
                'arabic_title' => 'المسند',
                'description'  => 'The largest hadith collection by a single scholar — containing 27,647 hadiths arranged by Companion narrators. Compiled over decades and later organized by his son Abdullah.',
                'created_at'   => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'   => $ahmad->id,
                'title'        => 'Kitab al-Sunnah',
                'arabic_title' => 'كتاب السنة',
                'description'  => "A work on matters of creed (aqeedah), outlining the beliefs of Ahl al-Sunnah wa al-Jama'ah.",
                'created_at'   => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'   => $ahmad->id,
                'title'        => 'Kitab al-Zuhd',
                'arabic_title' => 'كتاب الزهد',
                'description'  => 'A collection of narrations on asceticism and spiritual conduct from the Prophet ﷺ, Companions, and Successors.',
                'created_at'   => now(),
                'updated_at' => now(),
            ],
        ]);

        ScholarTeaching::insert([
            [
                'scholar_id'  => $ahmad->id,
                'title'       => 'Strict Adherence to Hadith',
                'content'     => "Ahmad ibn Hanbal's methodology placed the Quran and Sunnah above all else. He was extremely cautious about using ra'y (personal reasoning) and preferred even a weak hadith over rational analogy when no other text existed. His school is known for the most literal adherence to transmitted texts.",
                'order_index' => 1,
                'created_at'  => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'  => $ahmad->id,
                'title'       => "Quran is the Uncreated Word of Allah",
                'content'     => "Ahmad ibn Hanbal's most defining theological stance was his firm belief — against the Mu'tazilite Mihna — that the Quran is the eternal, uncreated Speech of Allah. This position, for which he was tortured, became the foundational creed of Ahl al-Sunnah.",
                'order_index' => 2,
                'created_at'  => now(),
                'updated_at' => now(),
            ],
        ]);

        ScholarQuote::insert([
            [
                'scholar_id'    => $ahmad->id,
                'quote_arabic'  => 'عجبت لمن يتعلم العلم ثم لا يعمل به كيف يتهنأ بطعام أو شراب',
                'quote_english' => 'I am amazed at a person who learns knowledge but does not act upon it — how can he enjoy food or drink?',
                'source'        => 'Manaqib al-Imam Ahmad, Ibn al-Jawzi',
                'created_at'    => now(),
                'updated_at' => now(),
            ],
            [
                'scholar_id'    => $ahmad->id,
                'quote_arabic'  => 'الناس محتاجون إلى العلم أكثر من احتياجهم إلى الطعام والشراب، لأن الطعام يُحتاج إليه في اليوم مرة أو مرتين، والعلم يُحتاج إليه في كل وقت',
                'quote_english' => 'People are in greater need of knowledge than they are of food and drink, for food and drink are needed once or twice a day, but knowledge is needed at every moment.',
                'source'        => 'Tabaqat al-Hanabilah, Ibn Abi Ya\'la',
                'created_at'    => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
