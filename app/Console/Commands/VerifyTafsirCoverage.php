<?php
// app/Console/Commands/VerifyTafsirCoverage.php

namespace App\Console\Commands;

use App\Models\Ayah;
use App\Models\AyahTafsir;
use App\Models\Surah;
use App\Models\Tafsir;
use Illuminate\Console\Command;

class VerifyTafsirCoverage extends Command
{
    protected $signature = 'quran:verify-tafsir {--tafsir= : Tafsir slug to verify}';
    protected $description = 'Health-check tafsir coverage — missing ayahs, empty surahs, duplicate/swap detection';

    public function handle(): int
    {
        $tafsir = Tafsir::where('slug', $this->option('tafsir'))->first();

        if (!$tafsir) {
            $this->error('Tafsir not found. Available slugs:');
            Tafsir::all()->each(fn($t) => $this->line("  {$t->slug}"));
            return self::FAILURE;
        }

        $this->info('');
        $this->info("🔍 Verifying: {$tafsir->name}");
        $this->info('================================');

        // ── Check 1: Kitni ayahs ka BILKUL koi tafsir nahi (fallback bhi nahi milega) ──
        $allAyahIds = Ayah::pluck('surah_id', 'id'); // [ayah_id => surah_id]
        $hasTafsir  = AyahTafsir::where('tafsir_id', $tafsir->id)->pluck('ayah_id')->flip();

        // Surah-wise group banao — jis surah ka kam se kam 1 ayah covered hai woh safe hai (fallback chalega)
        $surahsWithTafsir = Ayah::whereIn('id', $hasTafsir->keys())->pluck('surah_id')->unique();
        $allSurahIds      = Surah::pluck('id');
        $surahsWithZero   = $allSurahIds->diff($surahsWithTafsir);

        if ($surahsWithZero->isEmpty()) {
            $this->info('✅ Check 1: Har surah mein kam se kam 1 ayah ka tafsir hai (fallback safe hai)');
        } else {
            $this->error('❌ Check 1: In surahs mein BILKUL koi tafsir nahi (fallback bhi fail hoga):');
            Surah::whereIn('id', $surahsWithZero)->get()->each(
                fn($s) => $this->error("   - Surah {$s->number} ({$s->name_transliteration})")
            );
        }

        // ── Check 2: Boundary check — consecutive surahs ke beech swap detect karo ──
        $this->newLine();
        $this->info('🔍 Check 2: Surah boundary swap-check chal raha hai...');

        $suspiciousPairs = [];

        for ($num = 1; $num < 114; $num++) {
            $current = Surah::where('number', $num)->first();
            $next    = Surah::where('number', $num + 1)->first();
            if (!$current || !$next) continue;

            $lastAyah  = Ayah::where('surah_id', $current->id)->orderByDesc('number')->first();
            $firstAyah = Ayah::where('surah_id', $next->id)->orderBy('number')->first();
            if (!$lastAyah || !$firstAyah) continue;

            $lastText  = AyahTafsir::where('ayah_id', $lastAyah->id)->where('tafsir_id', $tafsir->id)->value('text');
            $firstText = AyahTafsir::where('ayah_id', $firstAyah->id)->where('tafsir_id', $tafsir->id)->value('text');

            // Agar dono ke pehle 50 characters same hain — suspicious (swap ho sakta hai)

            $lastSnippet  = substr($lastText, 200, 200);
            $firstSnippet = substr($firstText, 200, 200);

            if ($lastText && $firstText && $lastSnippet === $firstSnippet && strlen($lastText) > 200) {
                $suspiciousPairs[] = "Surah {$current->number} ↔ Surah {$next->number}";
            }
        }

        if (empty($suspiciousPairs)) {
            $this->info('✅ Check 2: Koi boundary swap detect nahi hua');
        } else {
            $this->error('❌ Check 2: Yeh pairs suspicious hain (manually check karo):');
            foreach ($suspiciousPairs as $pair) {
                $this->error("   - {$pair}");
            }
        }

        // ── Summary ──
        $this->newLine();
        $this->info('================================');
        $this->info('📊 Summary');
        $this->info("Total ayahs: " . $allAyahIds->count());
        $this->info("Ayahs with own tafsir: " . $hasTafsir->count());
        $this->info("Surahs with zero coverage: " . $surahsWithZero->count());
        $this->info("Suspicious boundary pairs: " . count($suspiciousPairs));

        return (empty($suspiciousPairs) && $surahsWithZero->isEmpty()) ? self::SUCCESS : self::FAILURE;
    }
}
