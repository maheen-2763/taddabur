<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\Changelog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangelogTest extends TestCase
{
    use RefreshDatabase;

    public function test_script_tags_are_stripped(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'cl');
        file_put_contents($path, "# Title\n\n<script>
    alert(1)
</script>\n\n[x](javascript:alert(1))");

        $html = Changelog::html($path);

        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        unlink($path);
    }

    public function test_missing_file_returns_empty_string(): void
    {
        $this->assertSame('', Changelog::html('/no/such/file.md'));
    }

    public function test_only_admin_can_view_page(): void
    {
        /** @var User $user */

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.changelog.index'))->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.changelog.index'))->assertOk()->assertSee('Changelog');
    }
}
