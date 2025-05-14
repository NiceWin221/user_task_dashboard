<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Task;
use App\Models\ActivityLog;
use App\Services\TaskService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_log_overdue_tasks_creates_activity_logs()
    {
        // Arrange
        $task1 = Task::factory()->create([
            'due_date' => now()->subDay(),
            'status' => 'in_progress',
        ]);

        $task2 = Task::factory()->create([
            'due_date' => now()->subDays(2),
            'status' => 'pending',
        ]);

        $taskNotOverdue = Task::factory()->create([
            'due_date' => now()->addDay(),
            'status' => 'pending',
        ]);

        $completedTask = Task::factory()->create([
            'due_date' => now()->subDay(),
            'status' => 'done',
        ]);

        $service = new TaskService();

        // Act
        $service->logOverdueTasks();

        // Assert
        $this->assertEquals(2, ActivityLog::count());

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $task1->assigned_to,
            'action' => 'task_overdue',
            'description' => 'Task overdue: ' . $task1->id . ' via scheduler',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $task2->assigned_to,
            'action' => 'task_overdue',
            'description' => 'Task overdue: ' . $task2->id . ' via scheduler',
        ]);

        $this->assertDatabaseMissing('activity_logs', [
            'user_id' => $taskNotOverdue->assigned_to,
        ]);

        $this->assertDatabaseMissing('activity_logs', [
            'user_id' => $completedTask->assigned_to,
        ]);
    }
}
