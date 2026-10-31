<?php

namespace Tests\Feature\Auth;

use App\Actions\DeleteUserAccount;
use App\Mail\AccountDeletedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_account_with_correct_password(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        Mail::assertSent(AccountDeletedMail::class, fn($mail) => $mail->hasTo($user->email));
    }

    public function test_wrong_password_does_not_delete_account(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('profile.destroy'), ['password' => 'wrong-password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        Mail::assertNothingSent();
    }

    public function test_the_only_admin_cannot_delete_own_account(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /**
     * Naya table (user_id ke saath) banaya aur DeleteUserAccount mein jodna bhool gaye?
     * To ye test fail hoga, aur privacy ka gap launch tak nahi pahunchega.
     */
    public function test_every_table_with_user_id_is_handled_by_account_deletion(): void
    {
        $tablesWithUserId = collect(Schema::getTables())
            ->pluck('name')
            ->filter(fn($table) => Schema::hasColumn($table, 'user_id'))
            ->values();

        $handled = array_merge(DeleteUserAccount::DELETE_TABLES, DeleteUserAccount::ANONYMIZE_TABLES);

        $this->assertSame(
            [],
            $tablesWithUserId->diff($handled)->values()->all(),
            'In tables mein user_id hai par DeleteUserAccount unhe nahi sambhalta.'
        );
    }
}
