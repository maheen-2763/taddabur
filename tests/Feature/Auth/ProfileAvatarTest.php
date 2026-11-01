<?php

namespace Tests\Feature\Auth;

use App\Actions\DeleteUserAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_user_can_upload_avatar_and_it_is_resized_to_a_square(): void
    {
        $user = User::factory()->create();
        /** @var User $user */

        $this->actingAs($user)
            ->post(route('profile.avatar'), ['avatar' => UploadedFile::fake()->image('me.jpg', 800, 600)])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);

        [$width, $height] = getimagesizefromstring(Storage::disk('public')->get($user->avatar));
        $this->assertSame([256, 256], [$width, $height]);
    }

    public function test_uploading_a_new_avatar_removes_the_old_file(): void
    {
        $user = User::factory()->create();
        /** @var User $user */

        $this->actingAs($user)->post(route('profile.avatar'), ['avatar' => UploadedFile::fake()->image('a.png', 300, 300)]);
        $first = $user->fresh()->avatar;

        $this->actingAs($user)->post(route('profile.avatar'), ['avatar' => UploadedFile::fake()->image('b.png', 300, 300)]);
        $second = $user->fresh()->avatar;

        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);
    }

    public function test_avatar_rejects_non_images_svg_and_oversized_files(): void
    {
        $user = User::factory()->create();
        /** @var User $user */

        foreach (
            [
                UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
                UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml'),
                UploadedFile::fake()->image('big.jpg', 600, 600)->size(3000),
            ] as $file
        ) {
            $this->actingAs($user)
                ->post(route('profile.avatar'), ['avatar' => $file])
                ->assertSessionHasErrors('avatar');
        }

        $this->assertNull($user->fresh()->avatar);
    }

    public function test_user_can_remove_avatar(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('profile.avatar'), ['avatar' => UploadedFile::fake()->image('a.jpg', 300, 300)]);
        $path = $user->fresh()->avatar;

        $this->actingAs($user)->delete(route('profile.avatar.destroy'))->assertRedirect(route('profile.edit'));

        $this->assertNull($user->fresh()->avatar);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_deleting_the_account_also_deletes_the_avatar_file(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('profile.avatar'), ['avatar' => UploadedFile::fake()->image('a.jpg', 300, 300)]);
        $path = $user->fresh()->avatar;
        Storage::disk('public')->assertExists($path);

        app(DeleteUserAccount::class)->handle($user->fresh());

        Storage::disk('public')->assertMissing($path);
    }

    public function test_guests_cannot_upload_an_avatar(): void
    {
        $this->post(route('profile.avatar'), ['avatar' => UploadedFile::fake()->image('a.jpg')])
            ->assertRedirect(route('login'));
    }
}
