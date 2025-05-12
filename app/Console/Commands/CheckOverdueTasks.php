<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Task;
use Illuminate\Console\Command;
use App\Services\TaskService;
use Illuminate\Support\Str;

class CheckOverdueTasks extends Command
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
    protected $description = 'Check overdue tasks and log them';

    protected $taskService;

    /**
     * Create a new command instance.
     *
     * @param TaskService $taskService
     * @return void
     */
    public function __construct(TaskService $taskService)
    {
        parent::__construct();
        $this->taskService = $taskService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->taskService->logOverdueTasks();

        if ($this->output) {
            $this->info('Overdue tasks have been logged.');
        }
    }
}
