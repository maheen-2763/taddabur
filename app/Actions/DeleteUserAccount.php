<?php

namespace App\Actions;

use App\Models\Backup;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Account delete: user ka personal data poori tarah hatata hai.
 * Sab kuch ek transaction mein hota hai, to beech mein fail hua to kuch bhi nahi hatega.
 */
class DeleteUserAccount
{
    /** Ye tables se user ki saari rows hata di jati hain. */
    public const DELETE_TABLES = [
        'bookmarks',
        'chapter_completions',
        'complaints',
        'hadith_reads',
        'listened_ayahs',
        'notes',
        'payment_orders', // legacy (paid era); Phase 3 mein table hat jayega
        'reading_progress',
        'reviews',
        'sessions',
        'subscriptions',
        'surah_progress',
        'user_preferences',
        'user_read_ayahs',
        'user_sessions',
    ];

    /**
     * Ye rows rakhi jati hain par user se alag (user_id = NULL). Abhi koi table nahi.
     * Donations 'donors' table mein alag se (bina user_id ke) rakhi jati hain.
     * Table yahan tabhi jodna jab uska user_id NULL ho sakta ho (nullable + nullOnDelete).
     */
    public const ANONYMIZE_TABLES = [];

    public function handle(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Pehle anonymize, taaki user delete hone par cascade inhe na hataye
            foreach (self::ANONYMIZE_TABLES as $table) {
                DB::table($table)->where('user_id', $user->id)->update(['user_id' => null]);
            }

            foreach (self::DELETE_TABLES as $table) {
                DB::table($table)->where('user_id', $user->id)->delete();
            }

            DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            if (Schema::hasTable('notifications')) {
                DB::table('notifications')
                    ->where('notifiable_type', $user->getMorphClass())
                    ->where('notifiable_id', $user->id)
                    ->delete();
            }

            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')
                    ->where('tokenable_type', $user->getMorphClass())
                    ->where('tokenable_id', $user->id)
                    ->delete();
            }

            Backup::where('created_by', $user->id)->update(['created_by' => null]);

            $user->delete();
        });
    }
}
