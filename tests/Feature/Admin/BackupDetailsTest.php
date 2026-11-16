<?php

namespace Tests\Feature\Admin;

use App\Support\BackupDetails;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PDO;
use Tests\Concerns\MakesBackups;
use Tests\TestCase;

class BackupDetailsTest extends TestCase
{
    use MakesBackups, RefreshDatabase;

    private string $dbPath;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        $this->dbPath = tempnam(sys_get_temp_dir(), 'bk');
    }

    protected function tearDown(): void
    {
        @unlink($this->dbPath);
        parent::tearDown();
    }

    private function sqlite(array $statements): void
    {
        $pdo = new PDO('sqlite:' . $this->dbPath);

        foreach ($statements as $sql) {
            $pdo->exec($sql);
        }

        $pdo = null;
    }

    public function test_describe_counts_rows_per_table_and_reads_the_last_migration(): void
    {
        $this->sqlite([
            'CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)',
            "INSERT INTO users (name) VALUES ('a'), ('b')",
            'CREATE TABLE empty_table (id INTEGER PRIMARY KEY)',
            'CREATE TABLE migrations (id INTEGER PRIMARY KEY, migration TEXT)',
            "INSERT INTO migrations (migration) VALUES ('2026_01_01_000000_first'), ('2026_02_01_000000_second')",
        ]);

        $details = BackupDetails::describe($this->dbPath);

        $this->assertSame(['empty_table' => 0, 'migrations' => 2, 'users' => 2], $details['tables']);
        $this->assertSame(3, $details['table_count']);
        $this->assertSame(4, $details['total_rows']);
        $this->assertSame('2026_02_01_000000_second', $details['last_migration']);
    }

    public function test_describe_ignores_sqlite_internal_tables(): void
    {
        $this->sqlite([
            'CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT)',
            "INSERT INTO posts (title) VALUES ('x')",
        ]);

        $this->assertSame(['posts' => 1], BackupDetails::describe($this->dbPath)['tables']);
    }

    public function test_describe_handles_a_missing_migrations_table_and_odd_table_names(): void
    {
        $this->sqlite([
            'CREATE TABLE "odd""name" (id INTEGER)',
            'INSERT INTO "odd""name" (id) VALUES (1)',
        ]);

        $details = BackupDetails::describe($this->dbPath);

        $this->assertSame(1, $details['tables']['odd"name']);
        $this->assertNull($details['last_migration']);
    }

    public function test_details_are_cast_to_an_array_and_persisted(): void
    {
        $backup = $this->makeBackup();
        $backup->update(['details' => ['tables' => ['users' => 3], 'table_count' => 1, 'total_rows' => 3, 'last_migration' => 'x']]);

        $this->assertSame(3, $backup->fresh()->details['total_rows']);
    }

    public function test_backups_page_shows_contents_sorted_by_row_count_and_flags_old_backups(): void
    {
        $this->makeBackup()->update(['details' => [
            'tables' => ['users' => 12, 'ayahs' => 6236],
            'table_count' => 2,
            'total_rows' => 6248,
            'last_migration' => '2026_11_01_000000_add_x',
        ]]);
        $this->makeBackup();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.backups'))
            ->assertOk()
            ->assertSee('Contents: 6,248 rows in 2 tables')
            ->assertSeeInOrder(['>ayahs<', '>users<'], false)
            ->assertSee('6,236')
            ->assertSee('2026_11_01_000000_add_x')
            ->assertSee('No contents recorded');
    }

    public function test_backups_page_escapes_table_names(): void
    {
        $this->makeBackup()->update(['details' => [
            'tables' => ['<b>x</b>' => 1],
            'table_count' => 1,
            'total_rows' => 1,
            'last_migration' => null,
        ]]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.backups'))
            ->assertOk()
            ->assertSee('&lt;b&gt;x&lt;/b&gt;', false)
            ->assertDontSee('<b>x</b>', false);
    }
}
