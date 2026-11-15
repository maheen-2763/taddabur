<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class GitHistory
{
    public const FILE = 'git-history.json';

    public const MAX_COMMITS = 200;

    /** Each commit starts with the record separator, then hash, committer date (ISO 8601) and subject split by the unit separator. */
    public const LOG_FORMAT = '--pretty=format:%x1e%h%x1f%cI%x1f%s';

    public const MAX_FILES_PER_COMMIT = 100;

    private const STATUSES = ['A', 'M', 'D', 'R', 'C', 'T'];

    /**
     * @return list<array{hash: string, date: string, message: string, files: list<array<string, string>>, files_total: int, counts: array<string, int>}>
     */
    public static function parse(string $raw): array
    {
        $commits = [];

        foreach (explode("\x1e", $raw) as $record) {
            $lines = preg_split('/\R/', trim($record));
            $parts = explode("\x1f", (string) array_shift($lines));

            if (count($parts) !== 3 || ! preg_match('/^[0-9a-f]{4,40}$/', $parts[0])) {
                continue;
            }

            try {
                $date = Carbon::parse($parts[1])->toIso8601String();
            } catch (Throwable) {
                continue;
            }

            $message = self::clean($parts[2], 200);

            if ($message === '') {
                continue;
            }

            $files = [];
            $counts = [];
            $total = 0;

            foreach ($lines as $line) {
                $file = self::parseFile($line);

                if ($file === null) {
                    continue;
                }

                $total++;
                $counts[$file['status']] = ($counts[$file['status']] ?? 0) + 1;

                if (count($files) < self::MAX_FILES_PER_COMMIT) {
                    $files[] = $file;
                }
            }

            $commits[] = [
                'hash' => $parts[0],
                'date' => $date,
                'message' => $message,
                'files' => $files,
                'files_total' => $total,
                'counts' => $counts,
            ];
        }

        return $commits;
    }

    /** Reads the exported file. Old files without file details still work. */
    public static function recent(int $limit = 30): array
    {
        if (! Storage::exists(self::FILE)) {
            return [];
        }

        $data = json_decode(Storage::get(self::FILE), true);

        if (! is_array($data)) {
            return [];
        }

        $commits = array_values(array_filter(array_map(self::normalize(...), $data)));

        return array_slice($commits, 0, $limit);
    }

    private static function normalize(mixed $commit): ?array
    {
        if (
            ! is_array($commit)
            || ! is_string($commit['hash'] ?? null)
            || ! is_string($commit['date'] ?? null)
            || ! is_string($commit['message'] ?? null)
        ) {
            return null;
        }

        $rawFiles = is_array($commit['files'] ?? null) ? $commit['files'] : [];
        $files = [];

        foreach ($rawFiles as $file) {
            if (
                is_array($file)
                && in_array($file['status'] ?? null, self::STATUSES, true)
                && is_string($file['path'] ?? null)
            ) {
                $files[] = ['status' => $file['status'], 'path' => $file['path']]
                    + (is_string($file['from'] ?? null) ? ['from' => $file['from']] : []);
            }
        }

        $counts = is_array($commit['counts'] ?? null)
            ? array_filter(array_map('intval', $commit['counts']))
            : array_count_values(array_column($files, 'status'));

        return [
            'hash' => $commit['hash'],
            'date' => $commit['date'],
            'message' => $commit['message'],
            'files' => $files,
            'files_total' => max((int) ($commit['files_total'] ?? 0), count($files)),
            'counts' => $counts,
        ];
    }

    private static function parseFile(string $line): ?array
    {
        if (! preg_match('/^([ACDMRT])\d*\t(.+)$/', $line, $match)) {
            return null;
        }

        $paths = array_map(fn(string $path) => self::clean($path, 255), explode("\t", $match[2]));

        if ($paths[0] === '') {
            return null;
        }

        // Renames and copies list the old path first, then the new one.
        if (in_array($match[1], ['R', 'C'], true) && count($paths) === 2 && $paths[1] !== '') {
            return ['status' => $match[1], 'path' => $paths[1], 'from' => $paths[0]];
        }

        return ['status' => $match[1], 'path' => $paths[0]];
    }

    private static function clean(string $text, int $max): string
    {
        return mb_substr(trim((string) preg_replace('/[[:cntrl:]]+/u', ' ', $text)), 0, $max);
    }
}
