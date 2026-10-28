<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


$id = 13; // <- deleted test user ki id yahan likhiye

$tables = collect(DB::select("select name from sqlite_master where type = 'table' and name not like 'sqlite_%'"))
    ->pluck('name')
    ->filter(fn($t) => Schema::hasColumn($t, 'user_id'));

foreach ($tables as $table) {
    $count = DB::table($table)->where('user_id', $id)->count();
    echo str_pad($table, 26) . $count . ($count ? '   <-- BACHA HUA DATA' : '') . PHP_EOL;
}
