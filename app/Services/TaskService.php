<?php

namespace App\Services;

use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Support\Str;

class TaskService
{
  public function logOverdueTasks()
  {
    $overdueTasks = Task::where('due_date', '<', now())
      ->where('status', '!=', 'completed')
      ->get();

    foreach ($overdueTasks as $task) {
      ActivityLog::create([
        'id' => Str::uuid(),
        'user_id' => $task->assigned_to,
        'action' => 'task_overdue',
        'description' => 'Task overdue: ' . $task->id . ' via scheduler',
        'logged_at' => now(),
      ]);
    }
  }
}
