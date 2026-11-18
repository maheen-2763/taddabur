<?php

namespace App\Support;

use FilesystemIterator;
use Generator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Number;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use UnexpectedValueException;

final class AppOverview
{
    public const CACHE_KEY = 'admin-app-overview';

    public const TTL_MINUTES = 60;

    /** Folders that are never scanned: huge and not part of the running app. */
    private const SKIPPED = ['node_modules'];

    /** Folders scanned for lines of code. */
    private const CODE_FOLDERS = ['app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'tests'];

    /** Larger files are counted as files but not read for lines. */
    private const LINE_COUNT_LIMIT_BYTES = 1_048_576;

    private readonly string $basePath;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = rtrim($basePath ?? base_path(), '/\\');
    }

    public static function cached(bool $refresh = false): array
    {
        if ($refresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, now()->addMinutes(self::TTL_MINUTES), fn() => app(self::class)->build());
    }

    public function build(): array
    {
        @set_time_limit(120);

        return [
            'generated_at' => now()->toIso8601String(),
            'size' => $this->size(),
            'code' => $this->code(),
            'structure' => $this->structure(),
            'environment' => $this->environment(),
            'packages' => $this->packages(),
        ];
    }

    public function size(): array
    {
        $entries = [];
        $skipped = [];

        foreach (scandir($this->basePath) ?: [] as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            if (in_array($name, self::SKIPPED, true)) {
                $skipped[] = $name;

                continue;
            }

            $path = $this->basePath . DIRECTORY_SEPARATOR . $name;
            $isDirectory = is_dir($path) && ! is_link($path);

            $entries[] = [
                'name' => $name,
                'type' => $isDirectory ? 'dir' : 'file',
                'bytes' => $isDirectory ? $this->directoryBytes($path) : (int) @filesize($path),
            ];
        }

        usort($entries, fn(array $a, array $b) => $b['bytes'] <=> $a['bytes']);

        return [
            'total_bytes' => array_sum(array_column($entries, 'bytes')),
            'entries' => $entries,
            'skipped' => $skipped,
            'storage' => $this->storageBreakdown(),
            'database_bytes' => $this->databaseBytes(),
        ];
    }

    public function code(): array
    {
        $types = [
            'PHP' => ['files' => 0, 'lines' => 0],
            'Blade' => ['files' => 0, 'lines' => 0],
            'JavaScript' => ['files' => 0, 'lines' => 0],
            'CSS' => ['files' => 0, 'lines' => 0],
        ];

        foreach (self::CODE_FOLDERS as $folder) {
            foreach ($this->files($this->basePath . DIRECTORY_SEPARATOR . $folder) as $file) {
                $type = $this->codeType($file->getFilename());

                if ($type === null) {
                    continue;
                }

                $types[$type]['files']++;
                $types[$type]['lines'] += $this->lineCount($file->getPathname(), $file->getSize());
            }
        }

        return [
            'types' => $types,
            'total_files' => array_sum(array_column($types, 'files')),
            'total_lines' => array_sum(array_column($types, 'lines')),
        ];
    }

    public function structure(): array
    {
        $routes = collect(Route::getRoutes()->getRoutes());
        $admin = $routes->filter(fn($route) => $route->uri() === 'admin' || str_starts_with($route->uri(), 'admin/'))->count();

        return [
            'app_groups' => $this->appGroups(),
            'routes' => ['total' => $routes->count(), 'admin' => $admin, 'public' => $routes->count() - $admin],
            'blade_views' => $this->countFiles('resources/views', '.blade.php'),
            'migrations' => $this->countFiles('database/migrations', '.php'),
            'seeders' => $this->countFiles('database/seeders', '.php'),
            'artisan_commands' => $this->countFiles('app/Console/Commands', '.php'),
            'test_files' => $this->countFiles('tests', 'Test.php'),
            'test_methods' => $this->testMethods(),
        ];
    }

    /** Safe configuration values only: never secrets and never anything from the .env file. */
    public function environment(): array
    {
        $extensions = get_loaded_extensions();
        sort($extensions, SORT_NATURAL | SORT_FLAG_CASE);

        return [
            'php' => PHP_VERSION,
            'laravel' => app()->version(),
            'environment' => (string) config('app.env'),
            'debug' => (bool) config('app.debug'),
            'timezone' => (string) config('app.timezone'),
            'database' => (string) config('database.default'),
            'session_driver' => (string) config('session.driver'),
            'cache_store' => (string) config('cache.default'),
            'queue' => (string) config('queue.default'),
            'mailer' => (string) config('mail.default'),
            'extensions' => $extensions,
        ];
    }

    public function packages(): array
    {
        $path = $this->basePath . DIRECTORY_SEPARATOR . 'composer.lock';
        $lock = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;

        $read = function (mixed $list): array {
            $packages = [];

            foreach (is_array($list) ? $list : [] as $package) {
                if (is_array($package) && is_string($package['name'] ?? null)) {
                    $packages[] = ['name' => $package['name'], 'version' => ltrim((string) ($package['version'] ?? ''), 'v')];
                }
            }

            usort($packages, fn(array $a, array $b) => strcmp($a['name'], $b['name']));

            return $packages;
        };

        $dev = $read($lock['packages-dev'] ?? null);

        return ['production' => $read($lock['packages'] ?? null), 'dev_count' => count($dev), 'dev' => $dev];
    }

    /** A plain Markdown summary that is safe to share: counts and names only. */
    public static function toMarkdown(array $overview): string
    {
        $bytes = fn(int $value): string => Number::fileSize($value, 1);
        $cell = fn(string $value): string => str_replace('|', '\\|', $value);
        $generated = Carbon::parse($overview['generated_at'])->timezone('Asia/Kolkata')->format('d M Y, h:i A');

        $size = $overview['size'];
        $code = $overview['code'];
        $structure = $overview['structure'];
        $env = $overview['environment'];
        $packages = $overview['packages'];

        $lines = ['# Taddabur app overview', '', "Generated: {$generated} IST", '', '## Size', ''];
        $lines[] = 'Total size (scanned): ' . $bytes($size['total_bytes']);
        $lines[] = '';
        $lines[] = '| Entry | Type | Size |';
        $lines[] = '|---|---|---|';

        foreach ($size['entries'] as $entry) {
            $lines[] = '| ' . $cell($entry['name']) . ' | ' . $entry['type'] . ' | ' . $bytes($entry['bytes']) . ' |';
        }

        $lines[] = '';

        if ($size['database_bytes'] !== null) {
            $lines[] = 'Database file: ' . $bytes($size['database_bytes']);
        }

        $lines[] = 'Storage: ' . implode(', ', array_map(fn($name, $value) => "{$name} " . $bytes($value), array_keys($size['storage']), $size['storage']));

        if ($size['skipped'] !== []) {
            $lines[] = 'Not scanned: ' . implode(', ', $size['skipped']);
        }

        $lines = [...$lines, '', '## Code', '', '| Type | Files | Lines |', '|---|---|---|'];

        foreach ($code['types'] as $type => $counts) {
            $lines[] = "| {$type} | {$counts['files']} | " . number_format($counts['lines']) . ' |';
        }

        $lines[] = '';
        $lines[] = "Total: {$code['total_files']} files, " . number_format($code['total_lines']) . ' lines';

        $routes = $structure['routes'];
        $lines = [...$lines, '', '## Structure', ''];
        $lines[] = "- Routes: {$routes['total']} ({$routes['admin']} admin, {$routes['public']} public)";
        $lines[] = "- Blade views: {$structure['blade_views']}";
        $lines[] = "- Migrations: {$structure['migrations']}";
        $lines[] = "- Seeders: {$structure['seeders']}";
        $lines[] = "- Artisan commands: {$structure['artisan_commands']}";
        $lines[] = "- Test files: {$structure['test_files']} ({$structure['test_methods']} tests)";
        $lines = [...$lines, '', '| App folder | PHP files |', '|---|---|'];

        foreach ($structure['app_groups'] as $group => $count) {
            $lines[] = '| ' . $cell($group) . " | {$count} |";
        }

        $lines = [...$lines, '', '## Environment', ''];
        $lines[] = "- PHP: {$env['php']}";
        $lines[] = "- Laravel: {$env['laravel']}";
        $lines[] = "- Environment: {$env['environment']} (debug " . ($env['debug'] ? 'on' : 'off') . ')';
        $lines[] = "- Timezone: {$env['timezone']}";
        $lines[] = "- Database: {$env['database']}, session: {$env['session_driver']}, cache: {$env['cache_store']}, queue: {$env['queue']}, mail: {$env['mailer']}";
        $lines[] = '- PHP extensions (' . count($env['extensions']) . '): ' . implode(', ', $env['extensions']);

        $lines = [...$lines, '', '## Packages', '', 'Production (' . count($packages['production']) . '):'];

        foreach ($packages['production'] as $package) {
            $lines[] = "- {$package['name']} {$package['version']}";
        }

        $lines[] = '';
        $lines[] = "Dev packages: {$packages['dev_count']}";

        return implode("\n", $lines) . "\n";
    }

    private function storageBreakdown(): array
    {
        $storage = $this->basePath . DIRECTORY_SEPARATOR . 'storage';
        $parts = ['backups' => 'app/backups', 'uploads' => 'app/public', 'logs' => 'logs', 'framework' => 'framework'];
        $result = [];

        foreach ($parts as $label => $relative) {
            $result[$label] = $this->directoryBytes($storage . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative));
        }

        return $result;
    }

    private function databaseBytes(): ?int
    {
        if (config('database.default') !== 'sqlite') {
            return null;
        }

        $name = (string) config('database.connections.sqlite.database');

        if ($name === '' || $name === ':memory:') {
            return null;
        }

        foreach ([$name, $this->basePath . DIRECTORY_SEPARATOR . $name] as $candidate) {
            if (is_file($candidate)) {
                return (int) filesize($candidate);
            }
        }

        return null;
    }

    private function appGroups(): array
    {
        $app = $this->basePath . DIRECTORY_SEPARATOR . 'app';
        $groups = [];

        foreach ($this->files($app) as $file) {
            if (! str_ends_with($file->getFilename(), '.php')) {
                continue;
            }

            $relative = str_replace('\\', '/', substr($file->getPath(), strlen($app) + 1));
            $segments = $relative === '' ? [] : explode('/', $relative);
            $group = $segments === [] ? '(app root)' : implode('/', array_slice($segments, 0, $segments[0] === 'Http' ? 2 : 1));

            $groups[$group] = ($groups[$group] ?? 0) + 1;
        }

        ksort($groups);

        return $groups;
    }

    private function countFiles(string $relative, string $suffix): int
    {
        $count = 0;

        foreach ($this->files($this->basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative)) as $file) {
            if (str_ends_with($file->getFilename(), $suffix)) {
                $count++;
            }
        }

        return $count;
    }

    /** Counts PHPUnit-style test methods and Pest it()/test() calls. */
    private function testMethods(): int
    {
        $count = 0;

        foreach ($this->files($this->basePath . DIRECTORY_SEPARATOR . 'tests') as $file) {
            if (str_ends_with($file->getFilename(), 'Test.php')) {
                $count += (int) preg_match_all('/function\s+test_|^\s*(?:it|test)\s*\(/m', (string) @file_get_contents($file->getPathname()));
            }
        }

        return $count;
    }

    private function codeType(string $filename): ?string
    {
        return match (true) {
            str_ends_with($filename, '.min.js'), str_ends_with($filename, '.min.css') => null,
            str_ends_with($filename, '.blade.php') => 'Blade',
            str_ends_with($filename, '.php') => 'PHP',
            str_ends_with($filename, '.js') => 'JavaScript',
            str_ends_with($filename, '.css') => 'CSS',
            default => null,
        };
    }

    private function lineCount(string $path, int $bytes): int
    {
        if ($bytes === 0 || $bytes > self::LINE_COUNT_LIMIT_BYTES) {
            return 0;
        }

        $content = @file_get_contents($path);

        if ($content === false || $content === '') {
            return 0;
        }

        return substr_count($content, "\n") + (str_ends_with($content, "\n") ? 0 : 1);
    }

    private function directoryBytes(string $path): int
    {
        $bytes = 0;

        foreach ($this->files($path) as $file) {
            $bytes += $file->getSize();
        }

        return $bytes;
    }

    /** @return Generator<SplFileInfo> Regular files only; symlinks are not followed, unreadable folders are skipped. */
    private function files(string $directory): Generator
    {
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY,
                RecursiveIteratorIterator::CATCH_GET_CHILD,
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && ! $file->isLink()) {
                    yield $file;
                }
            }
        } catch (UnexpectedValueException) {
            // The folder does not exist or cannot be read: it simply counts as empty.
        }
    }
}
