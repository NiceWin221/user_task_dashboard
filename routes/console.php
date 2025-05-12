<?php

use App\Console\Commands\CheckOverdueTasks;
use App\Services\TaskService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tasks:log-overdue', function (TaskService $taskService) {
    (new CheckOverdueTasks($taskService))->handle();
})->describe('Logs overdue tasks to activity log');
