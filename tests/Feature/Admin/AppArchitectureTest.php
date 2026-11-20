<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\AppArchitecture;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AppArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::forget(AppArchitecture::CACHE_KEY);
    }

    public function test_tables_are_assigned_to_the_right_module(): void
    {
        $expected = [
            'users' => 'Member data',
            'user_preferences' => 'Member data',
            'hadith_reads' => 'Member data',
            'bookmarks' => 'Member data',
            'surahs' => 'Quran',
            'ayah_translations' => 'Quran',
            'tafsir_overrides' => 'Quran',
            'reciter_word_timings' => 'Quran',
            'prophets' => 'Stories and prophets',
            'story_chapters' => 'Stories and prophets',
            'hadiths' => 'Hadith',
            'hadith_chapters' => 'Hadith',
            'hadiths_fts_data' => 'Hadith',
            'scholar_quotes' => 'Scholars',
            'allah_names' => 'Names and daily content',
            'daily_contents' => 'Names and daily content',
            'plans' => 'Support and donations',
            'reviews' => 'Feedback and admin',
            'activity_logs' => 'Feedback and admin',
            'jobs' => 'Framework',
            'something_new' => AppArchitecture::OTHER_GROUP,
        ];

        foreach ($expected as $table => $group) {
            $this->assertSame($group, AppArchitecture::groupFor($table), $table);
        }
    }

    public function test_every_table_in_the_schema_belongs_to_a_known_module(): void
    {
        $other = (new AppArchitecture)->tables()['groups'][AppArchitecture::OTHER_GROUP] ?? [];

        $this->assertSame([], $other, 'Add these tables to TABLE_GROUPS in AppArchitecture: ' . implode(', ', $other));
    }

    public function test_tables_include_the_real_foreign_keys(): void
    {
        $tables = (new AppArchitecture)->tables();

        $this->assertContains('users', $tables['groups']['Member data']);
        $this->assertContains('reviews', $tables['groups']['Feedback and admin']);
        $this->assertContains('hadiths_fts', $tables['groups']['Hadith']);
        $this->assertGreaterThan(20, $tables['count']);
        $this->assertContains(['child' => 'reviews', 'parent' => 'users', 'column' => 'user_id'], $tables['relationships']);
        $this->assertContains(['child' => 'hadiths', 'parent' => 'hadith_collections', 'column' => 'collection_id'], $tables['relationships']);
    }

    public function test_er_diagram_lists_every_foreign_key_that_touches_the_group(): void
    {
        $relationships = [
            ['child' => 'reviews', 'parent' => 'users', 'column' => 'user_id'],
            ['child' => 'x', 'parent' => 'y', 'column' => 'z'],
        ];

        $diagram = AppArchitecture::erDiagram(['reviews'], $relationships);

        $this->assertStringStartsWith('erDiagram', $diagram);
        $this->assertStringContainsString('    users ||--o{ reviews : "user_id"', $diagram);
        $this->assertStringNotContainsString('y ||--o{ x', $diagram);
        $this->assertSame('', AppArchitecture::erDiagram(['lonely'], $relationships));
    }

    public function test_er_diagram_cleans_unsafe_names(): void
    {
        $diagram = AppArchitecture::erDiagram(['odd-name'], [['child' => 'odd-name', 'parent' => '9lives', 'column' => 'a"b']]);

        $this->assertStringContainsString('    t_9lives ||--o{ odd_name : "ab"', $diagram);
    }

    public function test_flowchart_shows_the_counts(): void
    {
        $flow = AppArchitecture::flowchart([
            'routes' => 1187,
            'controllers' => 41,
            'services' => 6,
            'actions' => 7,
            'observers' => 3,
            'listeners' => 4,
            'models' => 38,
            'tables' => 57,
            'commands' => 12,
            'scheduled' => 2,
        ]);

        $this->assertStringStartsWith('flowchart TD', $flow);

        foreach (['1,187 routes', 'Controllers<br/>41', 'Services 6, Actions 7', 'Observers 3, Listeners 4', 'Models 38', '57 tables', 'Commands 12', 'Scheduled 2'] as $needle) {
            $this->assertStringContainsString($needle, $flow);
        }
    }

    public function test_routes_are_grouped_by_area_and_controller(): void
    {
        $routes = (new AppArchitecture)->routes();
        $areas = $routes['areas'];

        $this->assertSame(3, $areas['Admin']['Admin\\ReviewController']['routes']);
        $this->assertSame(['DELETE', 'GET', 'POST'], $areas['Admin']['BackupController']['methods']);
        $this->assertSame(4, $areas['Public and member pages']['FeedbackController']['routes']);
        $this->assertArrayHasKey('Auth\\PasswordController', $areas['Authentication']);

        $grouped = collect($areas)->flatten(1)->sum('routes');
        $this->assertSame($routes['total'], $routes['other'] + $grouped);
        $this->assertSame(Route::getRoutes()->count(), $routes['total']);
    }

    public function test_code_layers_list_classes_by_folder_including_subfolders(): void
    {
        $root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'arch-' . bin2hex(random_bytes(4));

        foreach (['app/Services/AService.php', 'app/Services/Sub/BService.php', 'app/Actions/DoThing.php', 'app/Services/notes.txt'] as $relative) {
            $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);

            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            file_put_contents($path, '<?php');
        }

        $layers = (new AppArchitecture($root))->codeLayers();

        $this->assertSame(['AService', 'Sub\\BService'], $layers['Services']);
        $this->assertSame(['DoThing'], $layers['Actions']);
        $this->assertSame([], $layers['Observers']);

        (new Filesystem)->deleteDirectory($root);
    }

    public function test_commands_include_the_app_commands_only(): void
    {
        $names = array_column((new AppArchitecture)->commands(), 'name');

        foreach (['backup:run', 'activity-log:prune', 'changelog:build-git'] as $expected) {
            $this->assertContains($expected, $names);
        }

        $this->assertNotContains('migrate', $names);
    }

    public function test_scheduled_tasks_are_read_from_the_scheduler(): void
    {
        app(Schedule::class)->command('backup:run')->dailyAt('02:00');

        $this->assertContains(
            ['expression' => '0 2 * * *', 'command' => 'backup:run'],
            (new AppArchitecture)->scheduled(),
        );
    }

    public function test_event_wiring_lists_app_listeners_only(): void
    {
        Event::listen('architecture.test', [\App\Listeners\LogAuthActivity::class, 'onLockout']);
        Event::listen('architecture.closure', fn() => null);

        $wiring = (new AppArchitecture)->eventWiring();

        $this->assertContains(['event' => 'architecture.test', 'listener' => 'App\\Listeners\\LogAuthActivity@onLockout'], $wiring);
        $this->assertContains(['event' => 'Illuminate\\Auth\\Events\\Failed', 'listener' => 'App\\Listeners\\LogAuthActivity@onFailed'], $wiring);
        $this->assertNotContains('architecture.closure', array_column($wiring, 'event'));
    }

    public function test_build_returns_every_section_with_a_matching_summary(): void
    {
        $architecture = (new AppArchitecture)->build();

        $this->assertSame(
            ['generated_at', 'summary', 'diagrams', 'routes', 'tables', 'code', 'commands', 'scheduled', 'events'],
            array_keys($architecture),
        );
        $this->assertSame(Route::getRoutes()->count(), $architecture['summary']['routes']);
        $this->assertSame($architecture['tables']['count'], $architecture['summary']['tables']);
        $this->assertSame(count($architecture['code']['Models']), $architecture['summary']['models']);
        $this->assertArrayHasKey('Quran', $architecture['diagrams']['modules']);
    }

    public function test_page_shows_every_section_and_the_diagrams(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.system.architecture'))
            ->assertOk()
            ->assertSee('Big picture')
            ->assertSee('Data modules')
            ->assertSee('Routes and controllers')
            ->assertSee('Background jobs')
            ->assertSee('Code layers')
            ->assertSee('Event wiring')
            ->assertSee('<pre class="mermaid', false)
            ->assertSee('flowchart TD')
            ->assertSee('erDiagram')
            ->assertSee('backup:run');
    }

    public function test_page_uses_the_cache_until_refresh_is_pressed(): void
    {
        $architecture = (new AppArchitecture)->build();
        $architecture['summary']['routes'] = 98765;
        $architecture['diagrams']['flow'] = AppArchitecture::flowchart($architecture['summary']);
        Cache::put(AppArchitecture::CACHE_KEY, $architecture, 60);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.system.architecture'))->assertSee('98,765 routes');

        $this->actingAs($admin)->post(route('admin.system.architecture.refresh'))
            ->assertRedirect(route('admin.system.architecture'))
            ->assertSessionHas('success');

        $this->actingAs($admin)->get(route('admin.system.architecture'))->assertDontSee('98,765 routes');
    }

    public function test_markdown_export_is_a_download_with_mermaid_blocks(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.system.architecture.export'))
            ->assertOk();

        $this->assertStringContainsString('attachment; filename="taddabur-architecture-', $response->headers->get('Content-Disposition'));

        $body = $response->getContent();

        foreach (['# Taddabur architecture', '```mermaid', 'flowchart TD', '## Data modules', '## Routes and controllers', '## Commands', '## Event wiring'] as $needle) {
            $this->assertStringContainsString($needle, $body);
        }
    }

    public function test_only_admins_can_use_the_architecture_pages(): void
    {
        $this->get(route('admin.system.architecture'))->assertRedirect();

        $user = User::factory()->create();

        /** @var User $user */

        $this->actingAs($user);
        $this->get(route('admin.system.architecture'))->assertForbidden();
        $this->get(route('admin.system.architecture.export'))->assertForbidden();
        $this->post(route('admin.system.architecture.refresh'))->assertForbidden();
    }
}
