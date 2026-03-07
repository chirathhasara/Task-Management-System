<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct()
    {
    }

    public function getAllTasks(User $user): Collection
    {
        return Task::getUserTasks($user->id);
    }

    public function getTasksByStatus(User $user, string $status): Collection
    {
        return Task::getUserTasksByStatus($user->id, $status);
    }

    public function getTaskById(int $taskId, User $user): ?Task
    {
        return Task::findByIdAndUser($taskId, $user->id);
    }

    public function createTask(array $data, User $user): Task
    {
        $data['user_id'] = $user->id;
        $data['status'] = $data['status'] ?? 'pending';
        
        return Task::createTask($data);
    }

    public function updateTask(Task $task, array $data): bool
    {
        return $task->updateTask($data);
    }

    public function markTaskAsCompleted(Task $task): bool
    {
        return $task->markAsCompleted();
    }

    public function markTaskAsPending(Task $task): bool
    {
        return $task->markAsPending();
    }

    public function deleteTask(Task $task, bool $forceDelete = false): bool
    {
        if ($forceDelete) {
            return $task->forceDeleteTask();
        }
        
        return $task->softDeleteTask();
    }

    public function verifyTaskOwnership(Task $task, User $user): bool
    {
        return $task->user_id === $user->id;
    }

    public function getTrashedTasks(User $user)
    {
        return Task::getTrashedTasks($user->id);
    }

    public function restoreTask(int $taskId, User $user): ?Task
    {
        $task = Task::findTrashedTask($taskId, $user->id);

        if (!$task) {
            return null;
        }

        $task->restoreTask();
        return $task->fresh();
    }

    public function permanentlyDeleteTask(int $taskId, User $user): bool
    {
        $task = Task::findTrashedTask($taskId, $user->id);

        if (!$task) {
            return false;
        }

        return $task->forceDeleteTask();
    }
}