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

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    private function line(string $hash, string $date, string $message): string
    {
        return implode("\x1f", [$hash, $date, $message]);
    }

    public function test_parse_reads_valid_lines_and_skips_malformed_ones(): void
    {
        $raw = implode("\n", [
            $this->line('abc1234', '2026-11-08T10:00:00+05:30', 'Fix audio bug'),
            'garbage line without separators',
            $this->line('not-a-hash', '2026-11-08T10:00:00+05:30', 'Bad hash'),
            $this->line('def5678', 'not a date', 'Bad date'),
            $this->line('0123abc', '2026-11-07T09:00:00+05:30', ''),
            $this->line('fff0000', '2026-11-06T09:00:00+05:30', 'Update styling'),
        ]);

        $commits = GitHistory::parse($raw);

        $this->assertSame(['abc1234', 'fff0000'], array_column($commits, 'hash'));
        $this->assertSame(['Fix audio bug', 'Update styling'], array_column($commits, 'message'));
    }

    public function test_parse_cleans_control_characters_and_limits_length(): void
    {
        $raw = $this->line('abc1234', '2026-11-08T10:00:00+05:30', "Fix\ttab" . str_repeat('x', 300));

        $message = GitHistory::parse($raw)[0]['message'];

        $this->assertStringNotContainsString("\t", $message);
        $this->assertLessThanOrEqual(200, mb_strlen($message));
    }

    public function test_parse_of_empty_output_returns_nothing(): void
    {
        $this->assertSame([], GitHistory::parse(''));
    }

    public function test_command_writes_parsed_commits(): void
    {
        Process::fake(['*' => Process::result(
            output: $this->line('abc1234', '2026-11-08T10:00:00+05:30', 'Fix audio bug') . "\n",
        )]);

        $this->artisan('changelog:build-git')->assertSuccessful();

        $this->assertSame('Fix audio bug', GitHistory::recent()[0]['message']);
    }

    public function test_command_keeps_existing_file_when_git_fails(): void
    {
        Storage::put(GitHistory::FILE, json_encode([
            ['hash' => 'abc1234', 'date' => '2026-11-08T10:00:00+05:30', 'message' => 'Old'],
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

    public function test_page_lists_commits_by_ist_date_and_escapes_text(): void
    {
        Storage::put(GitHistory::FILE, json_encode([
            ['hash' => 'abc1234', 'date' => '2026-11-08T22:30:00+00:00', 'message' => 'Fix <script>alert(1)</script>'],
        ]));

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.changelog.index'))
            ->assertOk()
            ->assertSee('Recent changes')
            ->assertSee('abc1234')
            ->assertSee('09 Nov 2026')
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_page_shows_hint_when_there_is_no_history_file(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.changelog.index'))
            ->assertOk()
            ->assertSee('changelog:build-git');
    }
}
