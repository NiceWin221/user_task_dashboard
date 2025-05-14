<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TaskFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_task()
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $staff = User::factory()->create(['role' => 'staff']);

        $payload = [
            'title' => 'New Task',
            'description' => 'Task description',
            'assigned_to' => $staff->id,
            'due_date' => now()->addDays(3)->toDateString(),
            'status' => 'pending',
        ];

        $response = $this->actingAs($manager)->postJson('/api/tasks', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    public function test_staff_cannot_create_task()
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $anotherStaff = User::factory()->create(['role' => 'staff']);

        $payload = [
            'title' => 'Staff Task',
            'assigned_to' => $anotherStaff->id,
            'due_date' => now()->addDays(2)->toDateString(),
        ];

        $response = $this->actingAs($staff)->postJson('/api/tasks', $payload);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Forbidden: Staff cannot create tasks']);
    }

    public function test_user_can_view_own_tasks()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);

        // Membuat task yang ditugaskan kepada staff
        $taskAssigned = Task::factory()->create([
            'assigned_to' => $staff->id,
            'created_by' => $admin->id,
        ]);

        // Login sebagai staff dan coba akses API untuk melihat task
        $response = $this->actingAs($staff)->getJson('/api/tasks');

        // Pastikan status response adalah 200 dan kedua task ada di dalam response
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $taskAssigned->id]);
    }

    public function test_admin_can_update_task()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $task = Task::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Title']);
    }

    public function test_creator_can_update_task_but_not_others()
    {
        $creator = User::factory()->create(['role' => 'manager']);
        $notCreator = User::factory()->create(['role' => 'manager']);
        $task = Task::factory()->create(['created_by' => $creator->id]);

        $response = $this->actingAs($notCreator)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Invalid Update',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_task()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $task = Task::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_unauthorized_user_cannot_delete_task()
    {
        $user = User::factory()->create(['role' => 'manager']);
        $task = Task::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }
}
