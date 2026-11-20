<?php

namespace App\Support;

use FilesystemIterator;
use Generator;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use UnexpectedValueException;

final class AppArchitecture
{
    public const CACHE_KEY = 'admin-app-architecture';

    public const TTL_MINUTES = 60;

    public const OTHER_GROUP = 'Other';

    /** First match wins, so the specific names must come before the wildcards that would also match them. */
    private const TABLE_GROUPS = [
        'Member data' => ['users', 'user_*', 'sessions', 'password_reset_tokens', 'bookmarks', 'notes', 'reading_progress', 'surah_progress', 'chapter_completions', 'listened_ayahs', 'hadith_reads'],
        'Quran' => ['surahs', 'juzs', 'ayahs', 'ayah_*', 'translations', 'tafsirs', 'tafsir_*', 'recitations', 'reciter_*'],
        'Stories and prophets' => ['prophets', 'stories', 'story_*'],
        'Hadith' => ['hadith*'],
        'Scholars' => ['scholar*'],
        'Names and daily content' => ['allah_names', 'prophet_names', 'daily_contents'],
        'Support and donations' => ['donors', 'payment_orders', 'subscriptions', 'plans', 'exchange_rates'],
        'Feedback and admin' => ['reviews', 'complaints', 'activity_logs', 'backups'],
        'Framework' => ['cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'migrations'],
    ];

    /** Code folders listed under "Code layers", relative to app/. */
    private const CODE_FOLDERS = [
        'Services' => 'Services',
        'Actions' => 'Actions',
        'Observers' => 'Observers',
        'Listeners' => 'Listeners',
        'Middleware' => 'Http/Middleware',
        'Mail' => 'Mail',
        'Support' => 'Support',
        'Enums' => 'Enums',
        'Models' => 'Models',
    ];

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
        $routes = $this->routes();
        $tables = $this->tables();
        $code = $this->codeLayers();
        $commands = $this->commands();
        $scheduled = $this->scheduled();

        $summary = [
            'routes' => $routes['total'],
            'controllers' => $routes['controller_count'],
            'services' => count($code['Services']),
            'actions' => count($code['Actions']),
            'observers' => count($code['Observers']),
            'listeners' => count($code['Listeners']),
            'models' => count($code['Models']),
            'tables' => $tables['count'],
            'commands' => count($commands),
            'scheduled' => count($scheduled),
        ];

        $modules = [];

        foreach ($tables['groups'] as $group => $names) {
            $modules[$group] = self::erDiagram($names, $tables['relationships']);
        }

        return [
            'generated_at' => now()->toIso8601String(),
            'summary' => $summary,
            'diagrams' => ['flow' => self::flowchart($summary), 'modules' => $modules],
            'routes' => $routes,
            'tables' => $tables,
            'code' => $code,
            'commands' => $commands,
            'scheduled' => $scheduled,
            'events' => $this->eventWiring(),
        ];
    }

    /** Routes grouped by area (by URL, so admin routes are found whatever the controller namespace is) and controller. */
    public function routes(): array
    {
        $areas = ['Admin' => [], 'Authentication' => [], 'Public and member pages' => []];
        $total = 0;
        $other = 0;

        foreach (Route::getRoutes()->getRoutes() as $route) {
            $total++;
            $controller = $route->getControllerClass();

            if ($controller === null || ! str_starts_with($controller, 'App\\')) {
                $other++;

                continue;
            }

            $uri = $route->uri();
            $area = match (true) {
                $uri === 'admin', str_starts_with($uri, 'admin/') => 'Admin',
                str_starts_with($controller, 'App\\Http\\Controllers\\Auth\\') => 'Authentication',
                default => 'Public and member pages',
            };

            $name = Str::after($controller, 'App\\Http\\Controllers\\');
            $areas[$area][$name]['routes'] = ($areas[$area][$name]['routes'] ?? 0) + 1;

            foreach ($route->methods() as $method) {
                if ($method !== 'HEAD') {
                    $areas[$area][$name]['methods'][$method] = true;
                }
            }
        }

        $controllers = [];

        foreach ($areas as $area => $entries) {
            ksort($entries);

            foreach ($entries as $name => $entry) {
                $methods = array_keys($entry['methods'] ?? []);
                sort($methods);

                $entries[$name] = ['routes' => $entry['routes'], 'methods' => $methods];
                $controllers[$name] = true;
            }

            $areas[$area] = $entries;
        }

        return ['areas' => $areas, 'total' => $total, 'other' => $other, 'controller_count' => count($controllers)];
    }

    /** Tables grouped by module, plus the foreign keys between them. SQLite only. */
    public function tables(): array
    {
        $empty = ['groups' => [], 'relationships' => [], 'count' => 0];

        if (DB::getDriverName() !== 'sqlite') {
            return $empty;
        }

        try {
            $names = array_map(
                fn($row) => $row->name,
                DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND substr(name, 1, 7) <> 'sqlite_' ORDER BY name"),
            );
        } catch (Throwable $e) {
            report($e);

            return $empty;
        }

        $grouped = [];

        foreach ($names as $name) {
            $grouped[self::groupFor($name)][] = $name;
        }

        $groups = [];

        foreach ([...array_keys(self::TABLE_GROUPS), self::OTHER_GROUP] as $group) {
            if (isset($grouped[$group])) {
                $groups[$group] = $grouped[$group];
            }
        }

        $relationships = [];

        foreach ($names as $table) {
            try {
                $rows = DB::select('PRAGMA foreign_key_list("' . str_replace('"', '""', $table) . '")');
            } catch (Throwable) {
                continue;
            }

            foreach ($rows as $row) {
                $relationships[] = ['child' => $table, 'parent' => (string) $row->table, 'column' => (string) $row->from];
            }
        }

        return ['groups' => $groups, 'relationships' => $relationships, 'count' => count($names)];
    }

    /** @return array<string, list<string>> Class names per code folder. */
    public function codeLayers(): array
    {
        $layers = [];

        foreach (self::CODE_FOLDERS as $label => $folder) {
            $directory = $this->basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder);
            $names = [];

            foreach ($this->phpFiles($directory) as $path) {
                $relative = substr($path, strlen($directory) + 1);
                $names[] = str_replace(DIRECTORY_SEPARATOR, '\\', substr($relative, 0, -4));
            }

            sort($names, SORT_NATURAL | SORT_FLAG_CASE);
            $layers[$label] = $names;
        }

        return $layers;
    }

    /** Artisan commands that belong to the app itself (not Laravel or packages). */
    public function commands(): array
    {
        try {
            $commands = [];

            foreach (Artisan::all() as $name => $command) {
                if (str_starts_with($command::class, 'App\\')) {
                    $commands[] = ['name' => $name, 'description' => $command->getDescription(), 'class' => class_basename($command)];
                }
            }

            usort($commands, fn(array $a, array $b) => strcmp($a['name'], $b['name']));

            return $commands;
        } catch (Throwable $e) {
            report($e);

            return [];
        }
    }

    public function scheduled(): array
    {
        try {
            app(Kernel::class)->bootstrap();

            return collect(app(Schedule::class)->events())->map(function ($event) {
                $command = (string) ($event->command ?? '');
                $summary = preg_match('/artisan[\'"]?\s+(.+)$/', $command, $match)
                    ? trim($match[1])
                    : ($event->description ?: 'Scheduled closure');

                return ['expression' => $event->expression, 'command' => $summary];
            })->values()->all();
        } catch (Throwable $e) {
            report($e);

            return [];
        }
    }

    /** Which app listener runs for which event. Closures and vendor listeners are left out. */
    public function eventWiring(): array
    {
        $dispatcher = Event::getFacadeRoot();

        if (! is_object($dispatcher) || ! method_exists($dispatcher, 'getRawListeners')) {
            return [];
        }

        $wiring = [];

        foreach ($dispatcher->getRawListeners() as $event => $listeners) {
            if (str_starts_with((string) $event, 'eloquent.')) {
                continue;
            }

            foreach ($listeners as $listener) {
                $label = match (true) {
                    is_string($listener) => $listener,
                    is_array($listener) && is_string($listener[0] ?? null) => $listener[0] . '@' . ($listener[1] ?? 'handle'),
                    default => null,
                };

                if ($label !== null && str_starts_with($label, 'App\\')) {
                    $wiring[] = ['event' => (string) $event, 'listener' => $label];
                }
            }
        }

        usort($wiring, fn(array $a, array $b) => [$a['event'], $a['listener']] <=> [$b['event'], $b['listener']]);

        return $wiring;
    }

    public static function groupFor(string $table): string
    {
        foreach (self::TABLE_GROUPS as $group => $patterns) {
            foreach ($patterns as $pattern) {
                if (fnmatch($pattern, $table)) {
                    return $group;
                }
            }
        }

        return self::OTHER_GROUP;
    }

    /**
     * Mermaid ER diagram of every foreign key that touches the given tables
     * (so links to other modules show up too). Empty string when there are none.
     *
     * @param  list<string>  $tables
     * @param  list<array{child: string, parent: string, column: string}>  $relationships
     */
    public static function erDiagram(array $tables, array $relationships): string
    {
        $members = array_flip($tables);
        $lines = ['erDiagram'];

        foreach ($relationships as $fk) {
            if (! isset($members[$fk['child']]) && ! isset($members[$fk['parent']])) {
                continue;
            }

            $lines[] = '    ' . self::entity($fk['parent']) . ' ||--o{ ' . self::entity($fk['child']) . ' : "' . str_replace('"', '', $fk['column']) . '"';
        }

        return count($lines) > 1 ? implode("\n", $lines) : '';
    }

    /** @param array<string, int> $summary */
    public static function flowchart(array $summary): string
    {
        $n = fn(string $key): string => number_format((int) ($summary[$key] ?? 0));

        return implode("\n", [
            'flowchart TD',
            '    visitors["Visitors and members"] --> routes',
            '    admin["Admin (one session)"] --> routes',
            '    routes["Routes and middleware<br/>' . $n('routes') . ' routes"] --> controllers["Controllers<br/>' . $n('controllers') . '"]',
            '    controllers --> logic["Services ' . $n('services') . ', Actions ' . $n('actions') . '<br/>Observers ' . $n('observers') . ', Listeners ' . $n('listeners') . '"]',
            '    logic --> models["Models ' . $n('models') . '"]',
            '    models --> db[("SQLite database<br/>' . $n('tables') . ' tables")]',
            '    commands["Commands ' . $n('commands') . '<br/>Scheduled ' . $n('scheduled') . '"] --> logic',
            '    commands --> db',
        ]);
    }

    /** Markdown with Mermaid blocks: renders on GitHub and in most Markdown viewers. */
    public static function toMarkdown(array $architecture): string
    {
        $generated = Carbon::parse($architecture['generated_at'])->timezone('Asia/Kolkata')->format('d M Y, h:i A');

        $lines = ['# Taddabur architecture', '', "Generated: {$generated} IST", '', '## Big picture', '', '```mermaid', $architecture['diagrams']['flow'], '```', '', '## Data modules', ''];

        foreach ($architecture['tables']['groups'] as $group => $names) {
            $lines[] = "### {$group} (" . count($names) . ' tables)';
            $lines[] = '';
            $lines[] = implode(', ', $names);
            $lines[] = '';

            if (($architecture['diagrams']['modules'][$group] ?? '') !== '') {
                $lines = [...$lines, '```mermaid', $architecture['diagrams']['modules'][$group], '```', ''];
            }
        }

        $lines = [...$lines, '## Routes and controllers', ''];

        foreach ($architecture['routes']['areas'] as $area => $controllers) {
            $lines[] = "### {$area}";
            $lines[] = '';

            foreach ($controllers as $name => $entry) {
                $lines[] = "- {$name}: {$entry['routes']} routes (" . implode(', ', $entry['methods']) . ')';
            }

            $lines[] = '';
        }

        $lines[] = "Closure and framework routes: {$architecture['routes']['other']}";
        $lines = [...$lines, '', '## Commands', ''];

        foreach ($architecture['commands'] as $command) {
            $lines[] = "- {$command['name']}: {$command['description']}";
        }

        $lines = [...$lines, '', '## Scheduled', ''];

        foreach ($architecture['scheduled'] as $task) {
            $lines[] = "- {$task['expression']}: {$task['command']}";
        }

        if ($architecture['scheduled'] === []) {
            $lines[] = 'No scheduled tasks found.';
        }

        $lines = [...$lines, '', '## Code layers', ''];

        foreach ($architecture['code'] as $layer => $names) {
            $lines[] = "- {$layer} (" . count($names) . '): ' . implode(', ', $names);
        }

        $lines = [...$lines, '', '## Event wiring', ''];

        foreach ($architecture['events'] as $wire) {
            $lines[] = '- ' . class_basename($wire['event']) . ' -> ' . class_basename($wire['listener']);
        }

        return implode("\n", $lines) . "\n";
    }

    private static function entity(string $name): string
    {
        $clean = (string) preg_replace('/[^A-Za-z0-9_]/', '_', $name);

        return preg_match('/^[A-Za-z]/', $clean) ? $clean : 't_' . $clean;
    }

    /** @return Generator<string> Paths of PHP files below the folder; a missing folder is simply empty. */
    private function phpFiles(string $directory): Generator
    {
        try {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));

            foreach ($iterator as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
                    yield $file->getPathname();
                }
            }
        } catch (UnexpectedValueException) {
            // The folder does not exist: it simply has no classes.
        }
    }
}
