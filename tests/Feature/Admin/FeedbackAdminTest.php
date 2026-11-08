<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Complaint;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FeedbackAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function review(int $rating = 5): Review
    {
        return Review::create([
            'user_id' => User::factory()->create()->id,
            'rating' => $rating,
            'category' => 'general',
        ]);
    }

    private function complaint(array $override = []): Complaint
    {
        return Complaint::create($override + [
            'user_id' => User::factory()->create()->id,
            'subject' => 'Audio is not playing.',
            'message' => 'The audio for Surah Yasin does not play.',
            'category' => 'bug',
        ]);
    }

    public function test_only_admin_can_use_feedback_admin_routes(): void
    {
        $review = $this->review();
        $complaint = $this->complaint();

        $user = User::factory()->create();

        /** @var User $user  */

        $requests = [
            ['GET', route('admin.reviews.index'), []],
            ['PATCH', route('admin.reviews.hide', $review), []],
            ['PATCH', route('admin.reviews.restore', $review), []],
            ['GET', route('admin.complaints.index'), []],
            ['GET', route('admin.complaints.show', $complaint), []],
            ['PATCH', route('admin.complaints.update', $complaint), ['status' => 'resolved']],
        ];

        foreach ($requests as [$method, $url, $data]) {
            $this->call($method, $url, $data)->assertRedirect();
        }

        $this->actingAs($user);
        foreach ($requests as [$method, $url, $data]) {
            $this->call($method, $url, $data)->assertForbidden();
        }

        $this->assertModelExists($review);
        $this->assertSame('open', $complaint->fresh()->status);
    }

    public function test_reviews_index_shows_visible_only_with_stats(): void
    {
        $this->review(5);
        $this->review(3);
        $this->review(1)->delete();

        $response = $this->actingAs($this->admin())->get(route('admin.reviews.index'))->assertOk();

        $this->assertSame(2, $response->viewData('reviews')->total());
        $this->assertSame(['count' => 2, 'average' => 4.0, 'hidden' => 1], $response->viewData('stats'));
    }

    public function test_hidden_tab_and_rating_filter(): void
    {
        $admin = $this->admin();
        $this->review(5);
        $this->review(2);
        $this->review(2)->delete();

        $hidden = $this->actingAs($admin)->get(route('admin.reviews.index', ['visibility' => 'hidden']));
        $this->assertSame(1, $hidden->viewData('reviews')->total());

        $twoStars = $this->actingAs($admin)->get(route('admin.reviews.index', ['rating' => 2]));
        $this->assertSame(1, $twoStars->viewData('reviews')->total());
    }

    public function test_invalid_review_filters_are_rejected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.reviews.index', ['rating' => 9]))
            ->assertSessionHasErrors('rating');
        $this->actingAs($admin)->get(route('admin.reviews.index', ['visibility' => 'x']))
            ->assertSessionHasErrors('visibility');
    }

    public function test_hide_and_restore_review_are_logged(): void
    {
        $admin = $this->admin();
        $review = $this->review();

        $this->actingAs($admin)->patch(route('admin.reviews.hide', $review))->assertSessionHas('success');
        $this->assertSoftDeleted($review);

        $this->actingAs($admin)->patch(route('admin.reviews.restore', $review))->assertSessionHas('success');
        $this->assertNotSoftDeleted($review);

        $hidden = ActivityLog::where('event', 'review.hidden')->sole();
        $restored = ActivityLog::where('event', 'review.restored')->sole();
        $this->assertSame($admin->id, $hidden->actor_id);
        $this->assertSame($admin->id, $restored->actor_id);
        $this->assertNull($hidden->properties);
    }

    public function test_hiding_an_already_hidden_review_is_404_and_not_logged(): void
    {
        $review = $this->review();
        $review->delete();

        $this->actingAs($this->admin())->patch(route('admin.reviews.hide', $review))->assertNotFound();

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_complaints_default_to_open_tab_with_counts(): void
    {
        $this->complaint();
        $this->complaint(['status' => 'resolved']);
        $this->complaint(['status' => 'in_progress']);
        $admin = $this->admin();

        $open = $this->actingAs($admin)->get(route('admin.complaints.index'))->assertOk();
        $this->assertSame(1, $open->viewData('complaints')->total());
        $this->assertEquals(['open' => 1, 'resolved' => 1, 'in_progress' => 1], $open->viewData('counts')->all());

        $all = $this->actingAs($admin)->get(route('admin.complaints.index', ['status' => 'all']));
        $this->assertSame(3, $all->viewData('complaints')->total());

        $resolved = $this->actingAs($admin)->get(route('admin.complaints.index', ['status' => 'resolved']));
        $this->assertSame(1, $resolved->viewData('complaints')->total());
    }

    public function test_invalid_complaint_status_filter_is_rejected(): void
    {
        $this->actingAs($this->admin())->get(route('admin.complaints.index', ['status' => 'bogus']))
            ->assertSessionHasErrors('status');
    }

    public function test_complaint_page_escapes_html_and_links_user(): void
    {
        $complaint = $this->complaint(['subject' => '<script>alert(1)</script>']);

        $this->actingAs($this->admin())->get(route('admin.complaints.show', $complaint))
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee(route('admin.users.show', $complaint->user_id));
    }

    public function test_update_saves_status_and_reply_with_reply_time(): void
    {
        $complaint = $this->complaint();

        $this->actingAs($this->admin())
            ->patch(route('admin.complaints.update', $complaint), [
                'status' => 'in_progress',
                'admin_reply' => 'We are looking into it',
            ])
            ->assertRedirect(route('admin.complaints.show', $complaint))
            ->assertSessionHas('success');

        $complaint->refresh();
        $this->assertSame('in_progress', $complaint->status);
        $this->assertSame('We are looking into it', $complaint->admin_reply);
        $this->assertNotNull($complaint->replied_at);
        $this->assertNull($complaint->resolved_at);
    }

    public function test_resolved_time_is_set_kept_and_cleared_on_reopen(): void
    {
        $admin = $this->admin();
        $complaint = $this->complaint();
        $url = route('admin.complaints.update', $complaint);

        $this->actingAs($admin)->patch($url, ['status' => 'resolved']);
        $first = $complaint->fresh()->resolved_at;
        $this->assertNotNull($first);

        $this->travel(2)->hours();
        $this->actingAs($admin)->patch($url, ['status' => 'resolved', 'admin_reply' => 'Theek kar diya']);
        $this->assertTrue($complaint->fresh()->resolved_at->equalTo($first));

        $this->actingAs($admin)->patch($url, ['status' => 'open']);
        $this->assertNull($complaint->fresh()->resolved_at);
    }

    public function test_clearing_the_reply_clears_reply_time(): void
    {
        $admin = $this->admin();
        $complaint = $this->complaint();
        $url = route('admin.complaints.update', $complaint);

        $this->actingAs($admin)->patch($url, ['status' => 'open', 'admin_reply' => 'First reply']);
        $this->actingAs($admin)->patch($url, ['status' => 'open', 'admin_reply' => '']);

        $complaint->refresh();
        $this->assertNull($complaint->admin_reply);
        $this->assertNull($complaint->replied_at);
    }

    public function test_only_status_change_is_logged_and_never_the_text(): void
    {
        $admin = $this->admin();
        $complaint = $this->complaint(['message' => 'private-message-text']);
        $url = route('admin.complaints.update', $complaint);

        $this->actingAs($admin)->patch($url, ['status' => 'in_progress', 'admin_reply' => 'secret-reply-text']);

        $log = ActivityLog::sole();
        $this->assertSame('complaint.status_changed', $log->event);
        $this->assertSame($admin->id, $log->actor_id);
        $this->assertSame(['status' => 'in_progress'], $log->properties);

        $raw = json_encode(DB::table('activity_logs')->get());
        $this->assertStringNotContainsString('secret-reply-text', $raw);
        $this->assertStringNotContainsString('private-message-text', $raw);

        // Sirf reply badla, status wahi: koi naya log nahi
        $this->actingAs($admin)->patch($url, ['status' => 'in_progress', 'admin_reply' => 'second reply']);
        $this->assertSame(1, ActivityLog::count());
    }

    public function test_update_validation_leaves_complaint_unchanged(): void
    {
        $admin = $this->admin();
        $complaint = $this->complaint();
        $url = route('admin.complaints.update', $complaint);

        $this->actingAs($admin)->patch($url, ['status' => 'bogus'])->assertSessionHasErrors('status');
        $this->actingAs($admin)->patch($url, ['status' => 'resolved', 'admin_reply' => str_repeat('a', 2001)])
            ->assertSessionHasErrors('admin_reply');

        $complaint->refresh();
        $this->assertSame('open', $complaint->status);
        $this->assertNull($complaint->admin_reply);
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_user_sees_admin_reply_and_status_on_feedback_page(): void
    {
        $complaint = $this->complaint();

        $this->actingAs($this->admin())->patch(route('admin.complaints.update', $complaint), [
            'status' => 'resolved',
            'admin_reply' => 'We have fixed it',
        ]);

        $this->actingAs($complaint->user)->get(route('feedback.index'))
            ->assertOk()
            ->assertSee('We have fixed it')
            ->assertSee('Resolved');
    }

    public function test_sidebar_links_and_open_complaint_badge(): void
    {
        $this->complaint();
        $this->complaint();
        $this->complaint(['status' => 'resolved']);

        $this->actingAs($this->admin())->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.reviews.index'))
            ->assertSee(route('admin.complaints.index'))
            ->assertSee('<span class="badge bg-danger ms-2">2</span>', false);
    }
}
