<?php

namespace Tests\Feature\Admin;

use App\Actions\RecordActivity;
use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\DailyContent;
use App\Models\Prophet;
use App\Models\Story;
use App\Models\StoryChapter;
use App\Models\User;
use App\Observers\AdminContentObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContentLogTest extends TestCase
{
    use RefreshDatabase;

    private function story(int $id = 5): Story
    {
        return (new Story)->forceFill(['id' => $id, 'title' => 'Old title'])->syncOriginal();
    }

    public function test_observer_is_registered_for_all_content_models(): void
    {
        foreach (AdminContentObserver::MODELS as $model) {
            foreach (['created', 'updated', 'deleted', 'restored'] as $event) {
                $this->assertTrue(
                    $model::getEventDispatcher()->hasListeners("eloquent.{$event}: {$model}"),
                    "{$model} is missing the {$event} listener",
                );
            }
        }
    }

    public function test_admin_changes_are_logged_with_type_id_and_field_names_only(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        $observer = app(AdminContentObserver::class);

        $story = $this->story();
        $observer->created($story);

        $story->title = 'New title';
        $story->summary = 'New summary';
        $story->syncChanges();
        $observer->updated($story);

        $observer->deleted($story);
        $observer->restored($story);

        $logs = ActivityLog::orderBy('id')->get();
        $this->assertSame(
            ['content.created', 'content.updated', 'content.deleted', 'content.restored'],
            $logs->pluck('event')->all(),
        );
        $this->assertSame([$admin->id], $logs->pluck('actor_id')->unique()->values()->all());
        $this->assertSame(['subject' => 'story', 'subject_id' => 5], $logs[0]->properties);
        $this->assertSame(
            ['subject' => 'story', 'subject_id' => 5, 'fields' => 'title, summary'],
            $logs[1]->properties,
        );

        $raw = json_encode(DB::table('activity_logs')->get());
        $this->assertStringNotContainsString('New title', $raw);
        $this->assertStringNotContainsString('New summary', $raw);
    }

    public function test_update_with_only_updated_at_change_is_not_logged(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $story = $this->story();
        $story->updated_at = now();
        $story->syncChanges();

        app(AdminContentObserver::class)->updated($story);

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_guest_and_regular_user_changes_are_not_logged(): void
    {
        $observer = app(AdminContentObserver::class);

        $observer->created($this->story());

        $user = User::factory()->create();
        /** @var User $user */

        $this->actingAs($user);
        $observer->deleted($this->story());

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_subject_names_for_every_content_model(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $observer = app(AdminContentObserver::class);

        foreach ([Story::class, StoryChapter::class, Prophet::class, DailyContent::class] as $class) {
            $observer->created((new $class)->forceFill(['id' => 7]));
        }

        $this->assertSame(
            ['story', 'story_chapter', 'prophet', 'daily_content'],
            ActivityLog::orderBy('id')->get()->map(fn($log) => $log->properties['subject'])->all(),
        );
    }

    public function test_activity_page_shows_content_event_details(): void
    {
        $admin = User::factory()->admin()->create();
        app(RecordActivity::class)->handle(ActivityEvent::ContentUpdated, $admin->id, [
            'subject' => 'story',
            'subject_id' => 5,
            'fields' => 'title, summary',
        ]);

        $this->actingAs($admin)->get(route('admin.activity-log.index'))
            ->assertOk()
            ->assertSee('Content updated')
            ->assertSee('subject: story')
            ->assertSee('fields: title, summary');
    }


    public function test_changes_to_deleted_at_alone_are_not_logged_as_updates(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        // Laravel's restore() saves the model, which fires "updated" before "restored".
        $story = $this->story();
        $story->deleted_at = now();
        $story->syncChanges();

        app(AdminContentObserver::class)->updated($story);

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_computed_fields_are_not_logged(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $observer = app(AdminContentObserver::class);

        $story = $this->story();
        $story->word_count = 500;
        $story->read_time_minutes = 3;
        $story->syncChanges();
        $observer->updated($story);
        $this->assertSame(0, ActivityLog::count());

        $story->title = 'New title';
        $story->word_count = 900;
        $story->syncChanges();
        $observer->updated($story);

        $this->assertSame('title', ActivityLog::sole()->properties['fields']);
    }
}
