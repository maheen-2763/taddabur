<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // FTS5 virtual table — content='hadiths' means it's index-only, no data duplication
        DB::statement("
            CREATE VIRTUAL TABLE hadiths_fts USING fts5(
                arabic,
                english,
                arabic_normalized,
                content='hadiths',
                content_rowid='id'
            )
        ");

        // INSERT trigger
        DB::statement("
            CREATE TRIGGER hadiths_ai AFTER INSERT ON hadiths BEGIN
                INSERT INTO hadiths_fts(rowid, arabic, english, arabic_normalized)
                VALUES (new.id, new.arabic, new.english, new.arabic);
            END
        ");

        // DELETE trigger
        DB::statement("
            CREATE TRIGGER hadiths_ad AFTER DELETE ON hadiths BEGIN
                INSERT INTO hadiths_fts(hadiths_fts, rowid, arabic, english, arabic_normalized)
                VALUES ('delete', old.id, old.arabic, old.english, old.arabic);
            END
        ");

        // UPDATE trigger (delete old entry, insert new)
        DB::statement("
            CREATE TRIGGER hadiths_au AFTER UPDATE ON hadiths BEGIN
                INSERT INTO hadiths_fts(hadiths_fts, rowid, arabic, english, arabic_normalized)
                VALUES ('delete', old.id, old.arabic, old.english, old.arabic);
                INSERT INTO hadiths_fts(rowid, arabic, english, arabic_normalized)
                VALUES (new.id, new.arabic, new.english, new.arabic);
            END
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS hadiths_ai');
        DB::statement('DROP TRIGGER IF EXISTS hadiths_ad');
        DB::statement('DROP TRIGGER IF EXISTS hadiths_au');
        DB::statement('DROP TABLE IF EXISTS hadiths_fts');
    }
};
