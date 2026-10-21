<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$rows = DB::select("select m.name as tbl, f.[from] as col, f.on_delete as on_delete from sqlite_master m, pragma_foreign_key_list(m.name) f where m.type = 'table' and f.[table] = 'users' order by m.name");

echo PHP_EOL . 'Tables with a foreign key to users:' . PHP_EOL;
foreach ($rows as $r) {
    echo str_pad($r->tbl, 28) . str_pad($r->col, 12) . $r->on_delete . PHP_EOL;
}

$withFk = collect($rows)->pluck('tbl')->all();

echo PHP_EOL . 'Tables with a user_id column but NO foreign key:' . PHP_EOL;
foreach (DB::select("select name from sqlite_master where type = 'table' and name not like 'sqlite_%'") as $t) {
    if (in_array($t->name, $withFk, true) || ! Schema::hasColumn($t->name, 'user_id')) {
        continue;
    }
    echo ' - ' . $t->name . ' (rows: ' . DB::table($t->name)->count() . ')' . PHP_EOL;
}
