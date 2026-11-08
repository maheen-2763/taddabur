<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_flash_has_autohide_and_close_and_appears_once(): void
    {
        $user = User::factory()->create();

        /** @var User $user */

        $html = $this->actingAs($user)
            ->followingRedirects()
            ->post(route('feedback.review.store'), ['rating' => 5])
            ->assertOk()
            ->assertSee('data-autohide="5000"', false)
            ->assertSee('data-autohide-close', false)
            ->getContent();

        $this->assertSame(1, substr_count($html, 'Your Review has been saved.'));
    }

    public function test_no_flash_markup_without_a_message(): void
    {
        $user = User::factory()->create();

        /** @var User $user */

        $this->actingAs($user)
            ->get(route('feedback.index'))
            ->assertOk()
            ->assertDontSee('flash-autohide', false);
    }
}
