<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class AuditCodePatterns extends Command
{
    protected $signature = 'project:audit-patterns';
    protected $description = 'Scan project for known bug-prone patterns (dark mode, js- classes, sync/async, lang arrays, number-vs-id)';

    public function handle()
    {
        $this->info('🔍 Project Pattern Audit Shuru...');
        $this->newLine();

        $this->auditDarkModeCss();
        $this->auditJsPrefixClasses();
        $this->auditSyncAsyncDuplication();
        $this->auditLanguageArrayAccess();
        $this->auditNumberVsIdUsage();

        $this->newLine();
        $this->info('✅ Audit complete.');
    }

    // ── 4. Dark Mode CSS override check ──────────────────────
    private function auditDarkModeCss()
    {
        $this->line('<fg=cyan>--- 4. Dark Mode CSS Override Check ---</>');

        $cssFiles = $this->findFiles(['public/css', 'resources/css'], ['css']);
        $missing = [];

        foreach ($cssFiles as $file) {
            $content = file_get_contents($file->getRealPath());

            preg_match_all('/:root\s*{([^}]*)}/s', $content, $rootMatches);
            preg_match_all('/\[data-bs-theme=["\']dark["\']\]\s*{([^}]*)}/s', $content, $darkMatches);

            $rootVars = [];
            foreach ($rootMatches[1] as $block) {
                preg_match_all('/(--[\w-]+)\s*:/', $block, $m);
                $rootVars = array_merge($rootVars, $m[1]);
            }

            $darkVars = [];
            foreach ($darkMatches[1] as $block) {
                preg_match_all('/(--[\w-]+)\s*:/', $block, $m);
                $darkVars = array_merge($darkVars, $m[1]);
            }

            $notOverridden = array_diff(array_unique($rootVars), array_unique($darkVars));

            if (!empty($notOverridden)) {
                $missing[$file->getRelativePathname()] = $notOverridden;
            }
        }

        if (empty($missing)) {
            $this->info('✅ Sab :root variables ka dark mode override mil gaya.');
        } else {
            foreach ($missing as $file => $vars) {
                $this->warn("⚠️  {$file}: " . implode(', ', $vars));
            }
        }
        $this->newLine();
    }

    // ── 5. js- prefix class double-use check ─────────────────
    private function auditJsPrefixClasses()
    {
        $this->line('<fg=cyan>--- 5. JS Hook Class (js-*) Styling Check ---</>');

        $cssFiles = $this->findFiles(['public/css', 'resources/css'], ['css']);
        $found = [];

        foreach ($cssFiles as $file) {
            $content = file_get_contents($file->getRealPath());
            // .js-something { ... } ya .js-something, .other { ... }
            preg_match_all('/\.(js-[\w-]+)\s*[,{]/', $content, $matches);

            if (!empty($matches[1])) {
                $found[$file->getRelativePathname()] = array_unique($matches[1]);
            }
        }

        if (empty($found)) {
            $this->info('✅ Koi .js-* class CSS files me styled nahi mili.');
        } else {
            foreach ($found as $file => $classes) {
                $this->warn("⚠️  {$file} me styling mili in par: " . implode(', ', $classes));
            }
        }
        $this->newLine();
    }

    // ── 6. Sync/Async fetch duplication check ────────────────
    private function auditSyncAsyncDuplication()
    {
        $this->line('<fg=cyan>--- 6. Sync/Async Fetch Duplication Check ---</>');

        $jsFiles = $this->findFiles(['public/js', 'resources/js'], ['js']);
        $functionNames = [];

        foreach ($jsFiles as $file) {
            $content = file_get_contents($file->getRealPath());
            preg_match_all('/(?:async\s+)?function\s+([\w]+)\s*\(/', $content, $matches);

            foreach ($matches[1] as $name) {
                $functionNames[] = strtolower(preg_replace('/(async)$/i', '', $name));
            }
        }

        $duplicates = array_filter(array_count_values($functionNames), fn($c) => $c > 1);

        if (empty($duplicates)) {
            $this->info('✅ Koi sync/async duplicate function names nahi mile.');
        } else {
            foreach ($duplicates as $name => $count) {
                $this->warn("⚠️  '{$name}' jaisa function {$count} baar mila (check karo sync+async dono to nahi hain).");
            }
        }
        $this->newLine();
    }

    // ── 7. Index-based language array access check ───────────
    private function auditLanguageArrayAccess()
    {
        $this->line('<fg=cyan>--- 7. Index-based Language Array Access Check ---</>');

        $files = $this->findFiles(['app', 'resources/views'], ['php', 'blade.php']);
        $found = [];

        foreach ($files as $file) {
            $content = file_get_contents($file->getRealPath());

            // Patterns jaise $x['hadith'][0], $x->translations[1], ->first() bina lang check ke
            preg_match_all('/\$\w+(?:\[[\'"]?\w+[\'"]?\])?\[(0|1)\]/', $content, $matches, PREG_OFFSET_CAPTURE);

            if (!empty($matches[0])) {
                $lines = [];
                foreach ($matches[0] as $match) {
                    $lineNum = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                    $lines[] = $lineNum;
                }
                $found[$file->getRelativePathname()] = $lines;
            }
        }

        if (empty($found)) {
            $this->info('✅ Koi index-based [0]/[1] array access nahi mila.');
        } else {
            foreach ($found as $file => $lines) {
                $this->warn("⚠️  {$file} — line(s): " . implode(', ', $lines) . " (manually verify karo, false-positive ho sakta hai)");
            }
        }
        $this->newLine();
    }

    // ── Bonus: highlight={{ number }} vs id check (deep-link bug) ─
    private function auditNumberVsIdUsage()
    {
        $this->line('<fg=cyan>--- Bonus: highlight={{ $h->number }} Deep-Link Check ---</>');

        $files = $this->findFiles(['resources/views'], ['blade.php']);
        $found = [];

        foreach ($files as $file) {
            $content = file_get_contents($file->getRealPath());

            if (preg_match_all('/highlight=\{\{\s*\$\w+->number\s*\}\}/', $content, $matches)) {
                $found[$file->getRelativePathname()] = count($matches[0]);
            }
            // id="hadith-{{ $h->number }}" wagera bhi pakdo
            if (preg_match_all('/id=["\']?\w+-\{\{\s*\$\w+->number\s*\}\}/', $content, $matches2)) {
                $found[$file->getRelativePathname()] = ($found[$file->getRelativePathname()] ?? 0) + count($matches2[0]);
            }
        }

        if (empty($found)) {
            $this->info('✅ Koi highlight/id ->number wala pattern nahi mila — sab id-based hai.');
        } else {
            foreach ($found as $file => $count) {
                $this->warn("⚠️  {$file}: {$count} jagah ->number use ho raha hai deep-link/id ke liye — check karo!");
            }
        }
        $this->newLine();
    }

    // ── Helper: files dhundo given directories + extensions me ──
    private function findFiles(array $dirs, array $extensions)
    {
        $files = [];
        foreach ($dirs as $dir) {
            $path = base_path($dir);
            if (!is_dir($path)) continue;

            $finder = new Finder();
            $finder->files()->in($path);

            foreach ($extensions as $ext) {
                $finder->name("*.{$ext}");
            }

            foreach ($finder as $file) {
                $files[] = $file;
            }
        }
        return $files;
    }
}
