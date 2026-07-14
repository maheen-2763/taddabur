<?php

namespace App\Services\Quran;

use App\Models\Juz;
use App\Models\SurahProgress;
use App\Models\UserReadAyah;
use Illuminate\Support\Facades\DB;

class QuranIndexService
{
    public function get(?int $userId = null): array
    {
        $progress = $this->getProgressMap($userId);

        return [
            'juzGroups'      => $this->getJuzGroups($progress),
            'completedCount' => count($progress['completedIds']),
        ];
    }

    private function getProgressMap(?int $userId): array
    {
        if (!$userId) {
            return ['readNumbers' => collect(), 'completedIds' => []];
        }

        // surah_id => collection of read ayah NUMBERS (not just a count)
        // needed to calculate progress per juz-slice, not just per whole surah
        $readNumbers = UserReadAyah::where('user_id', $userId)
            ->join('ayahs', 'ayahs.id', '=', 'user_read_ayahs.ayah_id')
            ->select('ayahs.surah_id', 'ayahs.number')
            ->get()
            ->groupBy('surah_id')
            ->map(fn($items) => $items->pluck('number'));

        $completedIds = SurahProgress::where('user_id', $userId)
            ->where('is_completed', true)
            ->pluck('surah_id')
            ->toArray();

        return ['readNumbers' => $readNumbers, 'completedIds' => $completedIds];
    }

    private function getJuzGroups(array $progress): \Illuminate\Support\Collection
    {
        $juzList = Juz::orderBy('number')->get();

        $surahsByNumber = DB::table('surahs')
            ->select('id', 'number', 'name_arabic', 'name_english', 'name_transliteration', 'ayah_count')
            ->get()
            ->keyBy('number');

        return $juzList->map(function ($juz) use ($progress, $surahsByNumber) {
            $mapping = $juz->verse_mapping ?? [];

            $surahs = collect($mapping)->map(function ($range, $surahNumber) use ($progress, $surahsByNumber) {
                $surahNumber = (int) $surahNumber;
                $surah = $surahsByNumber[$surahNumber] ?? null;

                if (!$surah) {
                    return null;
                }

                [$startAyah, $endAyah] = array_map('intval', explode('-', $range));
                $ayahsInSlice = max(1, $endAyah - $startAyah + 1);

                $isCompleted = in_array($surah->id, $progress['completedIds']);

                // Progress SPECIFIC to this juz-slice, not the whole surah
                $readInSlice = ($progress['readNumbers'][$surah->id] ?? collect())
                    ->filter(fn($n) => $n >= $startAyah && $n <= $endAyah)
                    ->count();

                $slicePercent = $isCompleted
                    ? 100
                    : min(99, (int) round(($readInSlice / $ayahsInSlice) * 100));


                // Resume point: agar kuch padha hai (par poora nahi), to next unread ayah dikhao
                $nextAyah = ($readInSlice > 0 && $readInSlice < $ayahsInSlice)
                    ? $startAyah + $readInSlice
                    : $startAyah;

                return (object) [
                    'id'                   => $surah->id,
                    'number'               => $surah->number,
                    'name_arabic'          => $surah->name_arabic,
                    'name_english'         => $surah->name_english,
                    'name_transliteration' => $surah->name_transliteration,
                    'ayah_count'           => $surah->ayah_count,
                    'start_ayah'           => $startAyah,
                    'end_ayah'             => $endAyah,
                    'read_count_in_slice'  => $readInSlice,   // NEW — "kitni padhi" ke liye
                    'has_progress'         => $readInSlice > 0 && $slicePercent < 100,
                    'is_continuation'      => $startAyah > 1,
                    'progress_percent'     => $slicePercent,
                ];
            })->filter()->values();

            return [
                'juz'          => $juz->number,
                'title'        => $juz->name_english,
                'title_ar'     => $juz->name_arabic,
                'surahs'       => $surahs,
                'juz_progress' => $surahs->isNotEmpty() ? round($surahs->avg('progress_percent')) : 0,
            ];
        })->values();
    }
}
