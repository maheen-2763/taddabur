<?php

namespace Tests\Feature;

use App\Actions\DeleteUserAccount;
use App\Models\Complaint;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Middleware\ThrottleRequests;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    /** @var User $user */

    private function complaintPayload(array $override = []): array
    {
        return $override + [
            'subject'  => 'Audio is not playing.',
            'message'  => 'The audio for Surah Yasin does not play.',
            'category' => 'bug',
        ];
    }

    public function test_guest_is_redirected_and_nothing_is_saved(): void
    {
        $this->get(route('feedback.index'))->assertRedirect();
        $this->post(route('feedback.review.store'), ['rating' => 5])->assertRedirect();
        $this->post(route('feedback.complaint.store'), $this->complaintPayload())->assertRedirect();

        $this->assertSame(0, Review::withTrashed()->count());
        $this->assertSame(0, Complaint::count());
    }

    public function test_page_renders_for_user(): void
    {
        $user = User::factory()->create();

        /** @var User $user */
        $this->actingAs($user)
            ->get(route('feedback.index'))
            ->assertOk()
            ->assertSee('Rate Taddabur');
    }

    public function test_review_is_created_and_updated_without_duplicate(): void
    {
        /** @var User $user */

        $user = User::factory()->create();

        $this->actingAs($user)->post(route('feedback.review.store'), ['rating' => 5, 'comment' => 'Mashallah'])
            ->assertRedirect(route('feedback.index'))
            ->assertSessionHas('success', 'JazakAllahu Khair! Your Review has been saved.');
        $this->actingAs($user)->post(route('feedback.review.store'), ['rating' => 3])
            ->assertSessionHas('success', 'Your Review has been updated.');

        $this->assertSame(1, Review::count());
        $review = Review::sole();
        $this->assertSame(3, $review->rating);
        $this->assertNull($review->comment);
        $this->assertSame('general', $review->category);
    }


    public function test_feedback_page_has_star_widget_counters_and_privacy_note(): void
    {
        $user = User::factory()->create();

        /** @var User $user */

        $this->actingAs($user)
            ->get(route('feedback.index'))
            ->assertOk()
            ->assertSee('class="star-rating', false)
            ->assertSee('id="rating-5"', false)
            ->assertSee('data-count-for="comment"', false)
            ->assertSee('data-count-for="message"', false)
            ->assertSee('private information', false);
    }

    public function test_existing_rating_is_prechecked(): void
    {
        $user = User::factory()->create();

        /** @var User $user */
        Review::create(['user_id' => $user->id, 'rating' => 4, 'category' => 'general']);

        $html = $this->actingAs($user)->get(route('feedback.index'))->getContent();

        $this->assertMatchesRegularExpression('/id="rating-4"[^>]*checked/', $html);
        $this->assertDoesNotMatchRegularExpression('/id="rating-5"[^>]*checked/', $html);
    }

    public function test_review_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        foreach ([0, 6, 'abc', null] as $bad) {
            $this->actingAs($user)->post(route('feedback.review.store'), ['rating' => $bad])
                ->assertSessionHasErrors('rating');
        }

        $this->actingAs($user)
            ->post(route('feedback.review.store'), ['rating' => 5, 'comment' => str_repeat('a', 1001)])
            ->assertSessionHasErrors('comment');

        $this->assertSame(0, Review::withTrashed()->count());
    }

    public function test_hidden_review_can_be_resubmitted_and_stays_hidden(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $review = Review::create(['user_id' => $user->id, 'rating' => 1, 'category' => 'general']);
        $review->delete();

        $this->actingAs($user)->post(route('feedback.review.store'), ['rating' => 4])
            ->assertRedirect(route('feedback.index'));

        $this->assertSame(1, Review::withTrashed()->count());
        $row = Review::withTrashed()->sole();
        $this->assertSame(4, $row->rating);
        $this->assertTrue($row->trashed());
    }

    public function test_user_deletes_own_review_permanently_and_only_own(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $other */
        $other = User::factory()->create();

        Review::create(['user_id' => $user->id, 'rating' => 2, 'category' => 'general'])->delete();
        Review::create(['user_id' => $other->id, 'rating' => 5, 'category' => 'general']);

        $this->actingAs($user)->delete(route('feedback.review.destroy'))
            ->assertRedirect(route('feedback.index'));

        $this->assertDatabaseMissing('reviews', ['user_id' => $user->id]);
        $this->assertDatabaseHas('reviews', ['user_id' => $other->id]);
    }

    public function test_complaint_is_created_open(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('feedback.complaint.store'), $this->complaintPayload())
            ->assertRedirect(route('feedback.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('complaints', [
            'user_id' => $user->id,
            'subject' => 'Audio nahi chal raha',
            'status' => 'open',
        ]);
    }

    public function test_complaint_cannot_set_status_owner_or_reply(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        /** @var User $user */
        /** @var User $other */

        $this->actingAs($user)->post(route('feedback.complaint.store'), $this->complaintPayload([
            'status' => 'resolved',
            'user_id' => $other->id,
            'admin_reply' => 'hacked',
        ]));

        $complaint = Complaint::sole();
        $this->assertSame($user->id, $complaint->user_id);
        $this->assertSame('open', $complaint->status);
        $this->assertNull($complaint->admin_reply);
    }

    public function test_complaint_validation(): void
    {
        $user = User::factory()->create();
        // Six requests are sent and throttle:5,60 returns 429 on the sixth; throttling has its own test.

        $this->withoutMiddleware(ThrottleRequests::class);

        /** @var User $user */

        $cases = [
            'subject'  => [['subject' => ''], ['subject' => str_repeat('a', 121)]],
            'message'  => [['message' => 'short'], ['message' => str_repeat('a', 2001)]],
            'category' => [['category' => 'payment'], ['category' => 'nonsense']],
        ];

        foreach ($cases as $field => $variants) {
            foreach ($variants as $override) {
                $this->actingAs($user)->post(route('feedback.complaint.store'), $this->complaintPayload($override))
                    ->assertSessionHasErrors($field);
            }
        }

        $this->assertSame(0, Complaint::count());
    }

    public function test_open_complaints_are_capped_at_five(): void
    {
        $user = User::factory()->create();
        /** @var User $user */

        foreach (range(1, 5) as $i) {
            Complaint::create(['user_id' => $user->id, 'subject' => "s{$i}", 'message' => 'x', 'category' => 'bug']);
        }

        $this->actingAs($user)->post(route('feedback.complaint.store'), $this->complaintPayload())
            ->assertSessionHasErrors('complaint');

        $this->assertSame(5, Complaint::count());
    }

    public function test_resolved_complaints_do_not_count_towards_cap(): void
    {
        $user = User::factory()->create();
        /** @var User $user */

        foreach (range(1, 5) as $i) {
            Complaint::create([
                'user_id' => $user->id,
                'subject' => "s{$i}",
                'message' => 'x',
                'category' => 'bug',
                'status' => 'resolved',
            ]);
        }

        $this->actingAs($user)->post(route('feedback.complaint.store'), $this->complaintPayload())
            ->assertSessionHas('success');

        $this->assertSame(6, Complaint::count());
    }

    public function test_complaint_form_is_throttled(): void
    {
        $user = User::factory()->create();

        /** @var User $user */

        foreach (range(1, 5) as $_) {
            $this->actingAs($user)->post(route('feedback.complaint.store'), $this->complaintPayload())
                ->assertRedirect(route('feedback.index'));
        }

        $this->actingAs($user)->post(route('feedback.complaint.store'), $this->complaintPayload())
            ->assertStatus(429);
    }

    public function test_user_sees_only_own_complaints_with_status_and_reply(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        /** @var User $user */
        /** @var User $other */

        Complaint::create([
            'user_id' => $user->id,
            'subject' => 'My-complaint',
            'message' => 'details here',
            'category' => 'bug',
            'status' => 'in_progress',
            'admin_reply' => 'We are looking into it',
        ]);
        Complaint::create(['user_id' => $other->id, 'subject' => 'SECRET-OTHER', 'message' => 'x', 'category' => 'bug']);

        $this->actingAs($user)->get(route('feedback.index'))
            ->assertOk()
            ->assertSee('My-complaint')
            ->assertSee('In progress')
            ->assertSee('We are looking into it')
            ->assertDontSee('SECRET-OTHER');
    }

    public function test_html_in_complaint_is_escaped_on_page(): void
    {
        $user = User::factory()->create();

        /** @var User $user */

        Complaint::create([
            'user_id' => $user->id,
            'subject' => '<script>alert(1)</script>',
            'message' => 'details here',
            'category' => 'bug',
        ]);

        $this->actingAs($user)->get(route('feedback.index'))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_account_delete_removes_reviews_and_complaints_even_hidden_ones(): void
    {
        $user = User::factory()->create();
        Review::create(['user_id' => $user->id, 'rating' => 2, 'category' => 'general'])->delete();
        Complaint::create(['user_id' => $user->id, 'subject' => 's', 'message' => 'x', 'category' => 'bug']);

        app(DeleteUserAccount::class)->handle($user);

        $this->assertDatabaseMissing('reviews', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('complaints', ['user_id' => $user->id]);
    }

    public function test_navbar_shows_feedback_icon_only_for_logged_in_users(): void
    {
        $user = User::factory()->create();
        /** @var User $user */

        $this->assertSame('bi-chat-square-text', config('icons.feedback'));
        $this->assertSame('bi-star-fill', config('icons.rating'));
        $this->assertSame('bi-exclamation-triangle', config('icons.complaint'));

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee(route('feedback.index'));

        Auth::logout();

        $this->get(route('home'))->assertOk()->assertDontSee(route('feedback.index'));
    }
}
