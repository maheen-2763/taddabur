<?php

namespace Tests\Feature;

use App\Actions\DeleteUserAccount;
use App\Models\Review;
use App\Models\User;
use App\Support\AppRating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AppRatingTest extends TestCase
{
    use RefreshDatabase;

    /** @return list<Review> */
    private function reviews(array $ratings): array
    {
        return array_map(fn(int $rating) => Review::create([
            'user_id' => User::factory()->create()->id,
            'rating' => $rating,
            'category' => 'general',
        ]), $ratings);
    }

    public function test_summary_is_hidden_until_there_are_enough_ratings(): void
    {
        $this->reviews([5, 5, 5, 5]);
        $this->assertNull(AppRating::summary());

        $this->reviews([4]);
        $this->assertSame(['count' => 5, 'average' => 4.8], AppRating::summary());
    }

    public function test_hidden_reviews_are_not_counted_and_the_cache_follows_hide_and_restore(): void
    {
        $reviews = $this->reviews([5, 5, 5, 5, 5]);
        $this->assertSame(5, AppRating::summary()['count']);

        $reviews[0]->delete();
        $this->assertNull(AppRating::summary());

        $reviews[0]->restore();
        $this->assertSame(5, AppRating::summary()['count']);
    }

    public function test_cache_is_cleared_when_a_user_removes_their_review_or_account(): void
    {
        $reviews = $this->reviews([5, 5, 5, 5, 5, 5]);
        $this->assertSame(6, AppRating::summary()['count']);

        $this->actingAs($reviews[0]->user)->delete(route('feedback.review.destroy'));
        $this->assertSame(5, AppRating::summary()['count']);

        app(DeleteUserAccount::class)->handle($reviews[1]->user);
        $this->assertNull(AppRating::summary());
    }

    public function test_summary_never_breaks_the_page_when_the_cache_fails(): void
    {
        Cache::shouldReceive('remember')->andThrow(new \RuntimeException('cache down'));

        $this->assertNull(AppRating::summary());
    }

    public function test_partial_shows_average_count_and_link(): void
    {
        $this->reviews([5, 4, 5, 4, 5]);

        $html = view('components.app-rating')->render();

        $this->assertStringContainsString('4.6 / 5 from 5 ratings', $html);
        $this->assertStringContainsString(route('feedback.index'), $html);
    }

    public function test_partial_renders_nothing_below_the_minimum(): void
    {
        $this->reviews([5, 5]);

        $this->assertSame('', trim(view('components.app-rating')->render()));
    }

    public function test_footer_shows_the_rating_on_public_pages(): void
    {
        $this->reviews([5, 5, 5, 5, 5]);

        $this->get(route('home'))->assertOk()->assertSee('5.0 / 5 from 5 ratings');
    }
}
