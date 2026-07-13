<?php

namespace App\Console\Commands;

use App\Models\Juz;
use App\Models\Surah;
use App\Services\QuranApiService;
use Illuminate\Console\Command;

class SyncJuzBoundaries extends Command
{
    protected $signature = 'quran:sync-juz-boundaries {--force : Skip confirmation prompt}';
    protected $description = 'Fetch and store Juz verse boundaries from Quran Foundation API';

    // Traditional juz names (first word/phrase of each juz) — verify against quran.com before trusting
    private array $juzNames = [
        1  => ['ar' => 'الم', 'en' => 'Alif Lam Meem'],
        2  => ['ar' => 'سَيَقُولُ', 'en' => 'Sayaqool'],
        3  => ['ar' => 'تِلْكَ ٱلرُّسُلُ', 'en' => 'Tilkal Rusulu'],
        4  => ['ar' => 'لَنْ تَنَالُوا', 'en' => 'Lan Tanaloo'],
        5  => ['ar' => 'وَٱلْمُحْصَنَاتُ', 'en' => 'Wal Mohsanatu'],
        6  => ['ar' => 'لَا يُحِبُّ ٱللهُ', 'en' => 'La Yuhibbullah'],
        7  => ['ar' => 'وَإِذَا سَمِعُوا', 'en' => 'Wa Iza Samiu'],
        8  => ['ar' => 'وَلَوْ أَنَّنَا', 'en' => 'Wa Lau Annana'],
        9  => ['ar' => 'قَالَ ٱلْمَلَأُ', 'en' => 'Qalal Malao'],
        10 => ['ar' => 'وَٱعْلَمُوا', 'en' => "Wa A'lamu"],
        11 => ['ar' => 'يَعْتَذِرُونَ', 'en' => 'Yatazeroon'],
        12 => ['ar' => 'وَمَا مِنْ دَآبَّةٍ', 'en' => "Wa Mamin Da'abatin"],
        13 => ['ar' => 'وَمَا أُبَرِّئُ', 'en' => 'Wa Ma Ubrioo'],
        14 => ['ar' => 'رُبَمَا', 'en' => 'Rubama'],
        15 => ['ar' => 'سُبْحَانَ ٱلَّذِى', 'en' => "Subhan Alladhi"],
        16 => ['ar' => 'قَالَ أَلَمْ', 'en' => 'Qala Alam'],
        17 => ['ar' => 'ٱقْتَرَبَ لِلنَّاسِ', 'en' => "Iqtaraba Lin-Nasi"],
        18 => ['ar' => 'قَدْ أَفْلَحَ', 'en' => 'Qad Aflaha'],
        19 => ['ar' => 'وَقَالَ ٱلَّذِينَ', 'en' => "Wa Qala Alladhina"],
        20 => ['ar' => 'أَمَّنْ خَلَقَ', 'en' => "A'man Khalaqa"],
        21 => ['ar' => 'أُتْلُ مَا أُوحِيَ', 'en' => 'Utlu Ma Oohiya'],
        22 => ['ar' => 'وَمَنْ يَّقْنُتْ', 'en' => 'Wa Man Yaqnut'],
        23 => ['ar' => 'وَمَا لِيَ', 'en' => 'Wa Mali'],
        24 => ['ar' => 'فَمَنْ أَظْلَمُ', 'en' => 'Faman Azlamu'],
        25 => ['ar' => 'إِلَيْهِ يُرَدُّ', 'en' => 'Ilayhi Yuruddu'],
        26 => ['ar' => 'حم', 'en' => 'Ha Meem'],
        27 => ['ar' => 'قَالَ فَمَا خَطْبُكُم', 'en' => 'Qala Fama Khatbukum'],
        28 => ['ar' => 'قَدْ سَمِعَ ٱللهُ', 'en' => 'Qad Sami Allah'],
        29 => ['ar' => 'تَبَارَكَ ٱلَّذِى', 'en' => 'Tabaraka Lladhi'],
        30 => ['ar' => 'عَمَّ', 'en' => 'Amma'],
    ];
    public function handle(QuranApiService $api): int
    {
        $this->info('Fetching juz data from Quran Foundation...');
        $rawJuzs = $api->fetchAllJuzs();

        if (empty($rawJuzs)) {
            $this->error('No data returned. Check API credentials / endpoint path.');
            return self::FAILURE;
        }

        $juzs = collect($rawJuzs)->unique('juz_number')->values();

        if ($juzs->count() !== 30) {
            $this->error('Expected 30 unique juz after dedupe, got ' . $juzs->count() . '. Aborting.');
            return self::FAILURE;
        }

        $surahMap = Surah::pluck('id', 'number'); // surah_number => id

        $this->info("\nSample verification (Al-Baqarah = surah 2):");
        foreach ($juzs as $juz) {
            if (isset($juz['verse_mapping']['2'])) {
                $this->line("  Juz {$juz['juz_number']}: Baqarah ayah {$juz['verse_mapping']['2']}");
            }
        }

        $this->info("\nJuz name check (verify against quran.com):");
        foreach ([1, 2, 15, 30] as $sample) {
            $this->line("  Juz {$sample}: {$this->juzNames[$sample]['en']} / {$this->juzNames[$sample]['ar']}");
        }

        if (!$this->option('force') && !$this->confirm("\nBoundaries AND names verified against a trusted source? Proceed to save?")) {
            $this->warn('Aborted — nothing saved.');
            return self::FAILURE;
        }

        foreach ($juzs as $juz) {
            $mapping = $juz['verse_mapping'];
            $chapterNumbers = array_map('intval', array_keys($mapping));

            $startChapterNum = min($chapterNumbers);
            $endChapterNum = max($chapterNumbers);

            [$startAyah] = explode('-', $mapping[(string) $startChapterNum]);
            [, $endAyah] = explode('-', $mapping[(string) $endChapterNum]);

            Juz::updateOrCreate(
                ['number' => $juz['juz_number']],
                [
                    'verse_mapping'  => $mapping,
                    'start_surah_id' => $surahMap[$startChapterNum] ?? null,
                    'start_ayah'     => (int) $startAyah,
                    'end_surah_id'   => $surahMap[$endChapterNum] ?? null,
                    'end_ayah'       => (int) $endAyah,
                    'name_arabic'    => $this->juzNames[$juz['juz_number']]['ar'],
                    'name_english'   => $this->juzNames[$juz['juz_number']]['en'],
                ]
            );
        }

        $this->info('✓ Saved all 30 juz boundaries.');
        return self::SUCCESS;
    }
}
