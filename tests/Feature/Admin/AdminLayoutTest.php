<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    use RefreshDatabase;

    private function dashboardHtml(): string
    {
        return $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->getContent();
    }

    public function test_layout_has_a_mobile_menu_toggle_backdrop_and_sidebar_id(): void
    {
        $html = $this->dashboardHtml();

        foreach (['id="adminSidebar"', 'id="adminMenuToggle"', 'id="adminBackdrop"', 'aria-controls="adminSidebar"', 'max-width: 991.98px', 'admin-nav-open'] as $needle) {
            $this->assertStringContainsString($needle, $html, "Missing {$needle} in the admin layout");
        }
    }

    public function test_wide_tables_and_cards_scroll_horizontally_instead_of_being_clipped(): void
    {
        $html = $this->dashboardHtml();

        foreach (['admin-table', 'admin-card'] as $class) {
            $this->assertMatchesRegularExpression('/\.' . $class . '\s*\{[^}]*overflow-x:\s*auto/', $html);
            $this->assertDoesNotMatchRegularExpression('/\.' . $class . '\s*\{[^}]*overflow:\s*hidden/', $html);
        }
    }
}
