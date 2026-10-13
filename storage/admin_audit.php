<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

$out = [];
$add = function (string $t = '') use (&$out) {
    $out[] = $t;
};
$head = function (string $t) use ($add) {
    $add();
    $add(str_repeat('=', 60));
    $add($t);
    $add(str_repeat('=', 60));
};

// ── 1. ENVIRONMENT ──────────────────────────────────────
$head('1. ENVIRONMENT');
$add('Laravel : ' . app()->version());
$add('PHP     : ' . PHP_VERSION);
$add('Env     : ' . config('app.env') . ' | debug=' . var_export(config('app.debug'), true));
$add('DB      : ' . config('database.default') . ' / ' . DB::connection()->getDatabaseName());

// ── 2. ROUTES (admin + layout me use hone wale) ─────────
$head('2. ADMIN ROUTES');
foreach (Route::getRoutes() as $r) {
    if (str_starts_with($r->uri(), 'admin') || str_starts_with((string) $r->getName(), 'admin')) {
        $add(sprintf(
            '%-10s %-40s %-35s %s | mw: %s',
            implode('|', $r->methods()),
            $r->uri(),
            $r->getName() ?? '-',
            ltrim($r->getActionName(), '\\'),
            implode(',', $r->gatherMiddleware())
        ));
    }
}
$add();
$add('Layout/Dashboard me use hone wale route names:');
foreach (
    [
        'admin.dashboard',
        'quran.index',
        'admin.stories.index',
        'admin.prophets.index',
        'admin.daily-reflections.index',
        'admin.users.index',
        'home',
        'logout'
    ] as $n
) {
    $add(sprintf('  %-32s %s', $n, Route::has($n) ? 'OK' : 'MISSING  <-- fix karna hai'));
}

// ── 3. DATABASE TABLES ──────────────────────────────────
$head('3. DATABASE TABLES (rows + columns)');
foreach (Schema::getTableListing() as $t) {
    try {
        $count = DB::table($t)->count();
    } catch (\Throwable $e) {
        $count = 'ERR';
    }
    $add(sprintf('%s  [rows: %s]', $t, $count));
    $add('    cols: ' . implode(', ', Schema::getColumnListing($t)));
}

// ── 4. ADMIN USERS (password/token kabhi nahi) ──────────
$head('4. ADMIN USERS');
if (Schema::hasTable('users')) {
    $cols = Schema::getColumnListing('users');
    $q = DB::table('users');
    if (in_array('is_admin', $cols))      $q->where('is_admin', 1);
    elseif (in_array('role', $cols))      $q->where('role', 'admin');
    else $add('Admin column (is_admin/role) nahi mila - sab users dikha raha hu (max 10)');
    $pick = array_values(array_intersect(['id', 'name', 'email', 'role', 'is_admin', 'email_verified_at', 'created_at'], $cols));
    foreach ($q->limit(10)->get($pick) as $u) $add('  ' . json_encode($u, JSON_UNESCAPED_UNICODE));
}

// ── 5. FILE INVENTORY ───────────────────────────────────
$head('5. FILE INVENTORY');
$list = function (string $dir, string $pattern) use ($add) {
    $base = base_path($dir);
    if (!is_dir($base)) {
        $add("  (missing) $dir");
        return;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) if (preg_match($pattern, $f->getFilename()))
        $add('  ' . str_replace([base_path() . DIRECTORY_SEPARATOR, '\\'], ['', '/'], $f->getPathname()));
};
$add('[Controllers]');
$list('app/Http/Controllers', '/\.php$/');
$add('[Models]');
$list('app/Models', '/\.php$/');
$add('[Middleware]');
$list('app/Http/Middleware', '/\.php$/');
$add('[Admin views]');
$list('resources/views/admin', '/\.blade\.php$/');
$add('[Migrations]');
$list('database/migrations', '/\.php$/');

// ── 6. PAID-FEATURE SCAN (free-forever cleanup list) ────
$head('6. PAID / SUBSCRIPTION REFERENCES (hatane ki list)');
$needle = '/subscription|payment|premium|basic|revenue|churn|stripe|razorpay|paypal|invoice|price|plan_id|\bplan\b|84\.5|1\.99|3\.99/i';
$hits = 0;
foreach (['app', 'routes', 'resources/views', 'config'] as $dir) {
    $base = base_path($dir);
    if (!is_dir($base)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if (!preg_match('/\.php$/', $f->getFilename())) continue;
        foreach (file($f->getPathname()) as $i => $line) {
            if (preg_match($needle, $line) && $hits++ < 400)
                $add(sprintf('  %s:%d  %s', str_replace([base_path() . DIRECTORY_SEPARATOR, '\\'], ['', '/'], $f->getPathname()), $i + 1, trim(mb_substr($line, 0, 110))));
        }
    }
}
$add("Total hits: $hits (max 400 dikhaye)");

// ── 7. DASHBOARD VARIABLES vs CONTROLLER ────────────────
$head('7. DASHBOARD VARIABLES CHECK');
$viewFile = base_path('resources/views/admin/dashboard.blade.php');
$ctrlFile = collect(glob(base_path('app/Http/Controllers/Admin/*Dashboard*.php')))->first();
if (is_file($viewFile) && $ctrlFile) {
    preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)/', file_get_contents($viewFile), $m);
    $skip = [
        'i',
        'c',
        'sub',
        'user',
        'surah',
        'star',
        'review',
        'count',
        'pct',
        'max',
        'total',
        'free',
        'basic',
        'premium',
        'freePct',
        'basicPct',
        'premiumPct',
        'r',
        'circ',
        'freeOff',
        'basicOff',
        'premiumOff',
        'verRate',
        'stars',
        'topSurahs',
        'maxReads',
        'usd',
        'inr',
        'errors',
        'loop',
        'slot',
        '__env',
        '__data',
        'obLevel',
        'this'
    ];
    $ctrl = file_get_contents($ctrlFile);
    $add('Controller: ' . basename($ctrlFile));
    foreach (array_diff(array_unique($m[1]), $skip) as $v)
        $add(sprintf('  %-26s %s', '$' . $v, str_contains($ctrl, "'$v'") || str_contains($ctrl, '$' . $v) ? 'defined' : 'NOT in controller  <-- fake/empty data'));
} else {
    $add('Dashboard view ya controller nahi mila (path check kijiye).');
}

// ── SAVE ────────────────────────────────────────────────
$path = storage_path('logs/admin_audit.txt');
file_put_contents($path, implode(PHP_EOL, $out));
echo PHP_EOL . "DONE -> $path" . PHP_EOL;
