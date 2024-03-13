<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartPendingPolls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poll:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'starts pending polls';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingPoll = Poll::query()->where([
            ['start_at', '<=', now()],
            ['end_at', '>=', now()],
            ['status', PollStatus::PENDING]
            ])->update(['status'=>PollStatus::STARTED]);

        return Command::SUCCESS;
    }
}
