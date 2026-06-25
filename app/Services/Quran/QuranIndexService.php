<?php

namespace App\Services\Quran;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\SurahProgress;


class QuranIndexService
{
    public function get(?int $userId = null): array
    {
        $surahs = $this->getSurahs();
        $counts = $this->getCounts($surahs);

        $progress = $this->getProgress($userId);
        $progressIds = $progress['ids'];

        $juzData = $this->BuildJuzTree($progressIds);

        return [
            'surahs'              => $surahs,
            'meccanCount'         => $counts['meccan'],
            'medinanCount'        => $counts['medinan'],
            'juzData'             => $juzData,
            'completedSurahIds'   => $progress['ids'],
            'completedCount'      => $progress['count'],
        ];
    }

    private function getSurahs()
    {
        return app('App\Services\QuranService')->getAllSurahs();
    }

    private function getCounts($surahs): array
    {
        return [
            'meccan'  => $surahs->where('revelation_type', 'meccan')->count(),
            'medinan' => $surahs->where('revelation_type', 'medinan')->count(),
        ];
    }

    private function getProgress(?int $userId): array
    {
        if (!$userId) {
            return [
                'ids' => [],
                'count' => 0
            ];
        }

        $completed = SurahProgress::where('user_id', $userId)
            ->where('is_completed', true)
            ->pluck('surah_id')
            ->toArray();

        return [
            'ids' => $completed,
            'count' => count($completed)
        ];
    }

    private function buildJuzTree(array $progressIds): \Illuminate\Support\Collection
    {
        $juzNames = JuzNameService::getAll();

        $completedLookup = array_flip($progressIds);

        $data = DB::table('juz_surah')
            ->join('surahs', 'surahs.id', '=', 'juz_surah.surah_id')
            ->select(
                'juz_surah.juz',
                'surahs.id',
                'surahs.number',
                'surahs.name_arabic',
                'surahs.name_english',
                'surahs.name_transliteration',
                'surahs.revelation_type',
                'surahs.ayah_count'
            )
            ->orderBy('juz_surah.juz')
            ->orderBy('surahs.number')
            ->get();

        return $data
            ->groupBy('juz')
            ->map(function ($items, $juzNumber) use ($juzNames, $completedLookup) {

                $surahs = $items->map(function ($surah) use ($completedLookup) {
                    return [
                        'id'                  => $surah->id,
                        'number'              => $surah->number,
                        'name_arabic'         => $surah->name_arabic,
                        'name_english'        => $surah->name_english,
                        'name_transliteration' => $surah->name_transliteration,
                        'type'                => $surah->revelation_type,
                        'ayah_count'          => $surah->ayah_count,
                        'completed'           => isset($completedLookup[$surah->id]),
                    ];
                });

                $total     = $surahs->count();
                $completed = $surahs->where('completed', true)->count();

                return [
                    'juz'   => (int) $juzNumber,
                    'title' => [
                        'ar' => $juzNames[$juzNumber]['ar'] ?? "الجزء {$juzNumber}",
                        'en' => $juzNames[$juzNumber]['en'] ?? "Juz {$juzNumber}",
                    ],

                    'surahs'   => $surahs->values(),

                    'progress' => [
                        'total'        => $total,
                        'completed'    => $completed,
                        'is_completed' => $total > 0 && $completed === $total,
                        'percentage'   => $total > 0 ? round(($completed / $total) * 100) : 0,
                    ],
                ];
            })
            ->values();
    }
}
