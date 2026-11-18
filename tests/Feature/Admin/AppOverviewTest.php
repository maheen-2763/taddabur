<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\AppOverview;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AppOverviewTest extends TestCase
{
    use RefreshDatabase;

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'overview-' . bin2hex(random_bytes(4));
        mkdir($this->root);

        // The page tests scan this small temporary tree instead of the real project.
        $this->app->bind(AppOverview::class, fn() => new AppOverview($this->root));
        Cache::forget(AppOverview::CACHE_KEY);
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->root);

        parent::tearDown();
    }

    private function put(string $relative, string $content): void
    {
        $path = $this->root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $content);
    }

    private function overview(): AppOverview
    {
        return new AppOverview($this->root);
    }

    public function test_size_sums_top_level_entries_and_skips_node_modules(): void
    {
        $this->put('app/A.php', str_repeat('a', 100));
        $this->put('vendor/pkg/x.txt', str_repeat('b', 1000));
        $this->put('storage/app/backups/b.zip', str_repeat('c', 500));
        $this->put('storage/app/public/u.png', str_repeat('d', 200));
        $this->put('storage/logs/laravel.log', str_repeat('e', 50));
        $this->put('.env', 'APP_KEY=secret-value');
        $this->put('node_modules/big/index.js', str_repeat('f', 9999));

        $size = $this->overview()->size();

        $this->assertSame(['vendor' => 1000, 'storage' => 750, 'app' => 100, '.env' => 20], array_column($size['entries'], 'bytes', 'name'));
        $this->assertSame(1870, $size['total_bytes']);
        $this->assertSame(['node_modules'], $size['skipped']);
        $this->assertSame(['backups' => 500, 'uploads' => 200, 'logs' => 50, 'framework' => 0], $size['storage']);
        $this->assertSame('file', collect($size['entries'])->firstWhere('name', '.env')['type']);
        $this->assertSame('dir', collect($size['entries'])->firstWhere('name', 'vendor')['type']);
    }

    public function test_database_size_reads_the_sqlite_file(): void
    {
        $database = $this->root . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'app.sqlite';
        $this->put('data/app.sqlite', str_repeat('x', 321));

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $database]);
        $this->assertSame(321, $this->overview()->size()['database_bytes']);

        config(['database.connections.sqlite.database' => ':memory:']);
        $this->assertNull($this->overview()->size()['database_bytes']);
    }

    public function test_code_counts_files_and_lines_by_type(): void
    {
        $this->put('app/Foo.php', "a\nb\nc");
        $this->put('tests/Unit/BarTest.php', 'x');
        $this->put('resources/views/page.blade.php', "a\nb\n");
        $this->put('resources/js/app.js', "1\n2\n3\n4\n");
        $this->put('resources/css/app.css', "1\n2\n3\n4\n5");
        $this->put('resources/js/vendor.min.js', "ignored\n");
        $this->put('database/seeders/data.json', "ignored\n");
        $this->put('vendor/lib/Lib.php', 'ignored');

        $code = $this->overview()->code();

        $this->assertSame([
            'PHP' => ['files' => 2, 'lines' => 4],
            'Blade' => ['files' => 1, 'lines' => 2],
            'JavaScript' => ['files' => 1, 'lines' => 4],
            'CSS' => ['files' => 1, 'lines' => 5],
        ], $code['types']);
        $this->assertSame(5, $code['total_files']);
        $this->assertSame(15, $code['total_lines']);
    }

    public function test_structure_counts_files_groups_routes_and_tests(): void
    {
        foreach (
            [
                'app/Root.php',
                'app/Actions/A.php',
                'app/Console/Commands/Run.php',
                'app/Http/Controllers/FooController.php',
                'app/Http/Controllers/Admin/BarController.php',
                'app/Http/Middleware/M.php',
                'app/Models/User.php',
                'app/Models/Review.php',
                'resources/views/a.blade.php',
                'resources/views/sub/b.blade.php',
                'database/migrations/m1.php',
                'database/migrations/m2.php',
                'database/seeders/S.php',
            ] as $path
        ) {
            $this->put($path, '<?php');
        }

        $this->put('tests/Feature/FooTest.php', "<?php\nclass FooTest {\n public function test_one() {}\n public function test_two() {}\n}\n");
        $this->put('tests/Pest/ExampleTest.php', "<?php\nit('works', fn () => 1);\ntest('also works', fn () => 1);\n");
        $this->put('tests/TestCase.php', '<?php');

        $structure = $this->overview()->structure();
        $allRoutes = Route::getRoutes()->count();

        $this->assertSame([
            '(app root)' => 1,
            'Actions' => 1,
            'Console' => 1,
            'Http/Controllers' => 2,
            'Http/Middleware' => 1,
            'Models' => 2,
        ], $structure['app_groups']);
        $this->assertSame(2, $structure['blade_views']);
        $this->assertSame(2, $structure['migrations']);
        $this->assertSame(1, $structure['seeders']);
        $this->assertSame(1, $structure['artisan_commands']);
        $this->assertSame(2, $structure['test_files']);
        $this->assertSame(4, $structure['test_methods']);
        $this->assertSame($allRoutes, $structure['routes']['total']);
        $this->assertGreaterThan(20, $structure['routes']['admin']);
        $this->assertSame($allRoutes, $structure['routes']['admin'] + $structure['routes']['public']);
    }

    public function test_packages_are_read_from_composer_lock(): void
    {
        $this->put('composer.lock', json_encode([
            'packages' => [
                ['name' => 'laravel/framework', 'version' => 'v13.0.1'],
                ['name' => 'brick/math', 'version' => '0.12.0'],
                'junk',
            ],
            'packages-dev' => [['name' => 'pestphp/pest', 'version' => 'v4.0.0']],
        ]));

        $packages = $this->overview()->packages();

        $this->assertSame([
            ['name' => 'brick/math', 'version' => '0.12.0'],
            ['name' => 'laravel/framework', 'version' => '13.0.1'],
        ], $packages['production']);
        $this->assertSame(1, $packages['dev_count']);
    }

    public function test_missing_or_broken_composer_lock_gives_empty_packages(): void
    {
        $this->assertSame(['production' => [], 'dev_count' => 0, 'dev' => []], $this->overview()->packages());

        $this->put('composer.lock', 'not json');
        $this->assertSame([], $this->overview()->packages()['production']);
    }

    public function test_environment_reports_safe_values_only(): void
    {
        $env = $this->overview()->environment();

        $this->assertSame(PHP_VERSION, $env['php']);
        $this->assertSame(app()->version(), $env['laravel']);
        $this->assertSame((bool) config('app.debug'), $env['debug']);
        $this->assertContains('json', $env['extensions']);
        $this->assertArrayNotHasKey('key', $env);
        $this->assertStringNotContainsString((string) config('app.key'), json_encode($env));
    }

    public function test_build_returns_every_section(): void
    {
        $this->assertSame(
            ['generated_at', 'size', 'code', 'structure', 'environment', 'packages'],
            array_keys($this->overview()->build()),
        );
    }

    public function test_page_shows_all_sections_and_never_the_env_contents(): void
    {
        $this->put('.env', 'APP_KEY=secret-value');

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.system.index'))
            ->assertOk()
            ->assertSee('App Overview')
            ->assertSee('Total size')
            ->assertSee('Size breakdown')
            ->assertSee('Structure')
            ->assertSee('Environment')
            ->assertSee('Packages')
            ->assertDontSee('secret-value');
    }

    public function test_page_uses_the_cache_until_refresh_is_pressed(): void
    {
        $overview = $this->overview()->build();
        $overview['environment']['php'] = '9.9.9';
        Cache::put(AppOverview::CACHE_KEY, $overview, 60);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.system.index'))->assertSee('9.9.9');

        $this->actingAs($admin)->post(route('admin.system.refresh'))
            ->assertRedirect(route('admin.system.index'))
            ->assertSessionHas('success');

        $this->actingAs($admin)->get(route('admin.system.index'))
            ->assertDontSee('9.9.9')
            ->assertSee(PHP_VERSION);
    }

    public function test_markdown_export_is_a_download_and_never_leaks_secrets(): void
    {
        $this->put('.env', 'APP_KEY=secret-value');

        $response = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.system.export'))
            ->assertOk();

        $this->assertStringContainsString('attachment; filename="taddabur-overview-', $response->headers->get('Content-Disposition'));

        $body = $response->getContent();

        foreach (['# Taddabur app overview', '## Size', '## Code', '## Structure', '## Environment', '## Packages'] as $heading) {
            $this->assertStringContainsString($heading, $body);
        }

        $this->assertStringNotContainsString('secret-value', $body);
    }

    public function test_debug_warning_only_shows_in_production(): void
    {
        config(['app.debug' => true, 'app.env' => 'production']);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.system.index'))
            ->assertSee('APP_DEBUG is on in production');

        Cache::forget(AppOverview::CACHE_KEY);
        config(['app.debug' => false]);

        $this->actingAs($admin)->get(route('admin.system.index'))
            ->assertDontSee('APP_DEBUG is on in production');
    }

    public function test_only_admins_can_use_the_overview(): void
    {
        $this->get(route('admin.system.index'))->assertRedirect();

        /** @var User $user */

        $user = User::factory()->create();

        $this->actingAs($user);
        $this->get(route('admin.system.index'))->assertForbidden();
        $this->get(route('admin.system.export'))->assertForbidden();
        $this->post(route('admin.system.refresh'))->assertForbidden();
    }

    public function test_sidebar_links_to_the_overview(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.system.index'))
            ->assertSee('App Overview');
    }
}
