<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyContent;

class SendDailyReflection extends Command
{
    protected $signature = 'reflection:send';

    protected $description = 'Publish daily reflection';

    public function handle()
    {
        $reflection = DailyContent::query()
            ->where('type', 'reflection')
            ->whereDate('scheduled_for', today())
            ->where('is_sent', false)
            ->first();

        if (!$reflection) {
            $this->info('No reflection found.');
            return Command::SUCCESS;
        }

        $reflection->update([
            'is_sent' => true
        ]);

        $this->info('Reflection published.');

        return Command::SUCCESS;
    }
}
