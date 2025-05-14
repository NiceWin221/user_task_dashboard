<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $query = Task::query();

        $query->where(function ($q) use ($user) {
            $q->where('assigned_to', $user->id)
                ->orWhere('created_by', $user->id);
        });

        $tasks = $query->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found'], 404);
        }

        return response()->json($tasks, 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role === 'staff') {
            return response()->json(['message' => 'Forbidden: Staff cannot create tasks'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|uuid|exists:users,id',
            'due_date' => 'required|date',
            'status' => 'in:pending,in_progress,done'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $assignedTo = User::find($request->input('assigned_to'));

        if (!$assignedTo) {
            return response()->json(['message' => 'User to assign not found'], 404);
        }

        if ($user->role === 'manager' && $assignedTo->role !== 'staff') {
            return response()->json(['message' => 'Manager can only assign tasks to staff'], 403);
        }

        $task = Task::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'assigned_to' => $assignedTo->id,
            'due_date' => $request->input('due_date'),
            'status' => $request->input('status', 'pending'),
            'created_by' => $user->id,
        ]);

        ActivityLog::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'action' => 'create_task',
            'description' => 'Created task: ' . $task->id,
            'logged_at' => now(),
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $user = Auth::user();

        if (
            $user->role !== 'admin' &&
            $task->created_by !== $user->id
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'uuid|exists:users,id',
            'status' => 'in:pending,in_progress,completed',
            'due_date' => 'date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $task->update($validated);

        ActivityLog::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'action' => 'update_task',
            'description' => 'Updated task: ' . $task->id,
            'logged_at' => now(),
        ]);

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $task = Task::findOrFail($id);

        // Cek apakah user adalah admin atau creator dari task
        if ($user->role !== 'admin' && $task->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $task->delete();

        // Simpan log aktivitas
        ActivityLog::create([
            'id' => Str::uuid(),
            'user_id'    => $user->id,
            'action'     => 'delete_task',
            'description' => 'Deleted task: ' . $task->id,
            'logged_at'  => now(),
        ]);

        return response()->json(['message' => 'Task deleted successfully.']);
    }

    public function show($id)
    {
        $task = Task::where('id', $id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json($task);
    }
}
