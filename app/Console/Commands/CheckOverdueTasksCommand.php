<?php

namespace App\Console\Commands;

use App\Jobs\CheckOverdueTasksJob;
use Illuminate\Console\Command;

class CheckOverdueTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue tasks and dispatch background notification job';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        CheckOverdueTasksJob::dispatch();
        $this->info('Overdue tasks check job dispatched successfully.');
        return Command::SUCCESS;
    }
}
