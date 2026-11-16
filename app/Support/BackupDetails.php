<?php

namespace App\Support;

use PDO;

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

        foreach ($names as $name) {
            $quoted = '"' . str_replace('"', '""', $name) . '"';
            $tables[$name] = (int) $pdo->query("SELECT COUNT(*) FROM {$quoted}")->fetchColumn();
        }

        $lastMigration = in_array('migrations', $names, true)
            ? $pdo->query('SELECT migration FROM migrations ORDER BY id DESC LIMIT 1')->fetchColumn()
            : false;

        $pdo = null; // Release the file handle (required on Windows).

        return [
            'tables' => $tables,
            'table_count' => count($tables),
            'total_rows' => array_sum($tables),
            'last_migration' => $lastMigration === false ? null : (string) $lastMigration,
        ];
    }
}
