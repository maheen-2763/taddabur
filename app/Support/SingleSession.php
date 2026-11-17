<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One active session per account.
 * - Admins: a second sign-in is blocked while another session is active (see LoginRequest).
 * - Users: the newest sign-in ends every older session.
 */
final class SingleSession
{
    /** An admin session counts as active until it has been idle this long. */
    public const ADMIN_IDLE_MINUTES = 30;

    public static function hasOtherActiveSession(Authenticatable $user, ?string $exceptSessionId = null): bool
    {
        return DB::table('sessions')
            ->where('user_id', $user->getAuthIdentifier())
            ->when($exceptSessionId, fn($query, $id) => $query->where('id', '!=', $id))
            ->where('last_activity', '>=', now()->subMinutes(self::ADMIN_IDLE_MINUTES)->getTimestamp())
            ->exists();
    }

    /** Deletes every session of the user except the given one and returns how many were removed. */
    public static function endOtherSessions(Authenticatable $user, ?string $exceptSessionId = null): int
    {
        return DB::table('sessions')
            ->where('user_id', $user->getAuthIdentifier())
            ->when($exceptSessionId, fn($query, $id) => $query->where('id', '!=', $id))
            ->delete();
    }

    /** Invalidates remember-me cookies on every device. Uses a query so a stale model cannot overwrite anything. */
    public static function rotateRememberToken(User $user): void
    {
        User::whereKey($user->getKey())->update(['remember_token' => Str::random(60)]);
    }
}
