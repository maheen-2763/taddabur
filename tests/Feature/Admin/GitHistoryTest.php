<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\GitHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GitHistoryTest extends TestCase
{
    use RefreshDatabase;

    private const DATE = '2026-11-08T10:00:00+05:30';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    /** Builds one commit the way `git log --name-status` prints it with GitHistory::LOG_FORMAT. */
    private function commit(string $hash, string $date, string $message, array $files = []): string
    {
        return "\x1e" . implode("\x1f", [$hash, $date, $message]) . "\n\n" . implode("\n", $files) . "\n";
    }

    public function test_parse_reads_commits_with_file_statuses(): void
    {
        $raw = $this->commit('abc1234', self::DATE, 'Fix audio bug', [
            "M\tapp/Models/User.php",
            "A\tapp/Support/New.php",
            "D\tapp/Old.php",
            "R100\told/Name.php\tnew/Name.php",
        ]) . $this->commit('fff0000', '2026-11-07T09:00:00+05:30', 'Update styling', ["M\tresources/css/app.css"]);

        $commits = GitHistory::parse($raw);
        $first = $commits[0];

        $this->assertSame(['abc1234', 'fff0000'], array_column($commits, 'hash'));
        $this->assertSame(['M', 'A', 'D', 'R'], array_column($first['files'], 'status'));
        $this->assertSame('new/Name.php', $first['files'][3]['path']);
        $this->assertSame('old/Name.php', $first['files'][3]['from']);
        $this->assertSame(4, $first['files_total']);
        $this->assertSame(['M' => 1, 'A' => 1, 'D' => 1, 'R' => 1], $first['counts']);
    }

    public function test_parse_skips_malformed_commits(): void
    {
        $raw = 'garbage before the first commit'
            . $this->commit('not-a-hash', self::DATE, 'Bad hash')
            . $this->commit('def5678', 'not a date', 'Bad date')
            . $this->commit('0123abc', self::DATE, '')
            . $this->commit('fff0000', self::DATE, 'Update styling');

        $this->assertSame(['fff0000'], array_column(GitHistory::parse($raw), 'hash'));
    }

    public function test_parse_caps_stored_files_but_counts_all_of_them(): void
    {
        $files = array_map(fn(int $i) => "M\tapp/File{$i}.php", range(1, 105));

        $commit = GitHistory::parse($this->commit('abc1234', self::DATE, 'Big commit', $files))[0];

        $this->assertCount(GitHistory::MAX_FILES_PER_COMMIT, $commit['files']);
        $this->assertSame(105, $commit['files_total']);
        $this->assertSame(['M' => 105], $commit['counts']);
    }

    public function test_parse_ignores_lines_that_are_not_file_changes(): void
    {
        $commit = GitHistory::parse($this->commit('abc1234', self::DATE, 'Mixed', [
            '?? untracked.txt',
            '',
            "X\tunknown-status.php",
            "M\t",
            "M\tapp/Ok.php",
        ]))[0];

        $this->assertSame(['app/Ok.php'], array_column($commit['files'], 'path'));
    }

    public function test_parse_cleans_control_characters_and_limits_length(): void
    {
        $raw = $this->commit('abc1234', self::DATE, "Fix\ttab" . str_repeat('x', 300), []);

        $message = GitHistory::parse($raw)[0]['message'];

        $this->assertStringNotContainsString("\t", $message);
        $this->assertLessThanOrEqual(200, mb_strlen($message));
    }

    public function test_parse_of_empty_output_returns_nothing(): void
    {
        $this->assertSame([], GitHistory::parse(''));
    }

    public function test_command_writes_commits_with_files(): void
    {
        Process::fake(['*' => Process::result(
            output: $this->commit('abc1234', self::DATE, 'Fix audio bug', ["M\tapp/A.php"]),
        )]);

        $this->artisan('changelog:build-git')->assertSuccessful();

        $commit = GitHistory::recent()[0];
        $this->assertSame('Fix audio bug', $commit['message']);
        $this->assertSame('app/A.php', $commit['files'][0]['path']);
        Process::assertRan(fn($process) => in_array('--name-status', (array) $process->command, true));
    }

    public function test_command_keeps_existing_file_when_git_fails(): void
    {
        Storage::put(GitHistory::FILE, json_encode([
            ['hash' => 'abc1234', 'date' => self::DATE, 'message' => 'Old'],
        ]));
        Process::fake(['*' => Process::result(errorOutput: 'fatal: not a git repository', exitCode: 128)]);

        $this->artisan('changelog:build-git')->assertFailed();

        $this->assertSame('Old', GitHistory::recent()[0]['message']);
    }

    public function test_command_rejects_invalid_limit(): void
    {
        Process::fake();

        $this->artisan('changelog:build-git --limit=0')->assertFailed();
        $this->artisan('changelog:build-git --limit=500')->assertFailed();

        Process::assertNothingRan();
    }

    public function test_recent_ignores_corrupt_or_incomplete_data(): void
    {
        Storage::put(GitHistory::FILE, 'not json');
        $this->assertSame([], GitHistory::recent());

        Storage::put(GitHistory::FILE, json_encode([['hash' => 'abc1234'], 'text']));
        $this->assertSame([], GitHistory::recent());
    }

    public function test_recent_accepts_the_old_format_without_files(): void
    {
        Storage::put(GitHistory::FILE, json_encode([
            ['hash' => 'abc1234', 'date' => self::DATE, 'message' => 'Old format'],
        ]));

        $commit = GitHistory::recent()[0];

        $this->assertSame([], $commit['files']);
        $this->assertSame(0, $commit['files_total']);
        $this->assertSame([], $commit['counts']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.changelog.index'))
            ->assertOk()
            ->assertSee('Old format');
    }

    public function test_page_lists_commits_with_files_by_ist_date_and_escapes_text(): void
    {
        Storage::put(GitHistory::FILE, json_encode([[
            'hash' => 'abc1234',
            'date' => '2026-11-08T22:30:00+00:00',
            'message' => 'Fix <script>alert(1)</script>',
            'files' => [
                ['status' => 'M', 'path' => 'app/Models/User.php'],
                ['status' => 'A', 'path' => '<b>x</b>.php'],
                ['status' => 'R', 'path' => 'new/Name.php', 'from' => 'old/Name.php'],
            ],
            'files_total' => 3,
            'counts' => ['M' => 1, 'A' => 1, 'R' => 1],
        ]]));

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.changelog.index'))
            ->assertOk()
            ->assertSee('Recent changes')
            ->assertSee('abc1234')
            ->assertSee('09 Nov 2026')
            ->assertSee('1 modified')
            ->assertSee('1 added')
            ->assertSee('app/Models/User.php')
            ->assertSee('old/Name.php')
            ->assertSee('&lt;b&gt;x&lt;/b&gt;.php', false)
            ->assertDontSee('<b>x</b>.php', false)
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_page_shows_how_many_files_are_hidden_by_the_cap(): void
    {
        Storage::put(GitHistory::FILE, json_encode([[
            'hash' => 'abc1234',
            'date' => self::DATE,
            'message' => 'Big commit',
            'files' => [['status' => 'M', 'path' => 'app/A.php']],
            'files_total' => 3,
            'counts' => ['M' => 3],
        ]]));

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.changelog.index'))
            ->assertSee('+ 2 more file(s)');
    }

    public function test_page_shows_hint_when_there_is_no_history_file(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.changelog.index'))
            ->assertOk()
            ->assertSee('changelog:build-git');
    }
}
