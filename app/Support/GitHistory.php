<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class GitHistory
{
    public const FILE = 'git-history.json';

    /** Short hash, committer date (ISO 8601) and subject line, separated by the ASCII unit separator. */
    public const LOG_FORMAT = '--pretty=format:%h%x1f%cI%x1f%s';

    /** @return list<array{hash: string, date: string, message: string}> */
    public static function parse(string $raw): array
    {
        $commits = [];

        foreach (preg_split('/\R/', trim($raw)) as $line) {
            $parts = explode("\x1f", $line);

            if (count($parts) !== 3 || ! preg_match('/^[0-9a-f]{4,40}$/', $parts[0])) {
                continue;
            }

            try {
                $date = Carbon::parse($parts[1])->toIso8601String();
            } catch (Throwable) {
                continue;
            }

            $message = trim((string) preg_replace('/[[:cntrl:]]+/u', ' ', $parts[2]));

            if ($message === '') {
                continue;
            }

            $commits[] = ['hash' => $parts[0], 'date' => $date, 'message' => mb_substr($message, 0, 200)];
        }

        return $commits;
    }

    /** @return list<array{hash: string, date: string, message: string}> */
    public static function recent(int $limit = 30): array
    {
        if (! Storage::exists(self::FILE)) {
            return [];
        }

        $data = json_decode(Storage::get(self::FILE), true);

        if (! is_array($data)) {
            return [];
        }

        $valid = array_filter($data, fn($commit) => is_array($commit) && isset($commit['hash'], $commit['date'], $commit['message']));

        return array_slice(array_values($valid), 0, $limit);
    }
}
