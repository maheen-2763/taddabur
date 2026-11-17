<?php

namespace App\Support;

use PDO;
use PDOException;

final class BackupDetails
{
    /**
     * Describes a SQLite snapshot: row count per table, totals and the last applied migration.
     *
     * @return array{tables: array<string, int>, table_count: int, total_rows: int, last_migration: ?string}
     */
    public static function describe(string $sqlitePath): array
    {
        $pdo = new PDO('sqlite:' . $sqlitePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $names = $pdo
            ->query("SELECT name FROM sqlite_master WHERE type = 'table' AND substr(name, 1, 7) <> 'sqlite_' ORDER BY name")
            ->fetchAll(PDO::FETCH_COLUMN);

        $tables = [];
        $skipped = [];

        foreach ($names as $name) {
            $quoted = '"' . str_replace('"', '""', $name) . '"';

            try {
                $tables[$name] = (int) $pdo->query("SELECT COUNT(*) FROM {$quoted}")->fetchColumn();
            } catch (PDOException $e) {
                // For example a full-text index whose content table lost a column: report it instead of losing every count.
                $skipped[$name] = mb_substr((string) preg_replace('/^SQLSTATE\[[^\]]*\]: /', '', $e->getMessage()), 0, 150);
            }
        }

        $lastMigration = in_array('migrations', $names, true)
            ? $pdo->query('SELECT migration FROM migrations ORDER BY id DESC LIMIT 1')->fetchColumn()
            : false;

        $pdo = null; // Release the file handle (required on Windows).

        return [
            'tables' => $tables,
            'skipped' => $skipped,
            'table_count' => count($tables),
            'total_rows' => array_sum($tables),
            'last_migration' => $lastMigration === false ? null : (string) $lastMigration,
        ];
    }
}
