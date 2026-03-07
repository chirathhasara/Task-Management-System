<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function indexView(): View
    {
        return view('tasks.index');
    }

    public function createView(): View
    {
        return view('tasks.create');
    }

    public function editView(int $id): View
    {
        return view('tasks.edit');
    }

    public function showView(int $id): View
    {
        return view('tasks.show');
    }

    public function recycleBinView(): View
    {
        return view('tasks.recycle-bin');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $status = $request->query('status');
            $perPage = $request->query('per_page', 10);
            
            if ($status && in_array($status, ['pending', 'completed'])) {
                $tasks = $this->taskService->getTasksByStatus($request->user(), $status, $perPage);
            } else {
                $tasks = $this->taskService->getAllTasks($request->user(), $perPage);
            }

            return response()->json([
                'success' => true,
                'data' => $tasks->items(),
                'pagination' => [
                    'current_page' => $tasks->currentPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                    'last_page' => $tasks->lastPage(),
                    'from' => $tasks->firstItem(),
                    'to' => $tasks->lastItem(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tasks.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', Task::class);

            $task = $this->taskService->createTask($request->validated(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully.',
                'data' => $task,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create task.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->getTaskById($id, $request->user());

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.',
                ], 404);
            }

            $this->authorize('view', $task);

            return response()->json([
                'success' => true,
                'data' => $task,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve task.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->getTaskById($id, $request->user());

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.',
                ], 404);
            }

            $this->authorize('update', $task);

            $this->taskService->updateTask($task, $request->validated());
            $task->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully.',
                'data' => $task,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->getTaskById($id, $request->user());

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.',
                ], 404);
            }

            $this->authorize('delete', $task);

            $this->taskService->deleteTask($task);

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete task.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsCompleted(Request $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->getTaskById($id, $request->user());

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.',
                ], 404);
            }

            $this->authorize('update', $task);

            $this->taskService->markTaskAsCompleted($task);
            $task->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Task marked as completed.',
                'data' => $task,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsPending(Request $request, int $id): JsonResponse
    {
        try {
            $task = $this->taskService->getTaskById($id, $request->user());

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.',
                ], 404);
            }

            $this->authorize('update', $task);

            $this->taskService->markTaskAsPending($task);
            $task->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Task marked as pending.',
                'data' => $task,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function trashed(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);
            $trashedTasks = $this->taskService->getTrashedTasks($request->user(), $perPage);

            return response()->json([
                'success' => true,
                'data' => $trashedTasks->items(),
                'pagination' => [
                    'current_page' => $trashedTasks->currentPage(),
                    'per_page' => $trashedTasks->perPage(),
                    'total' => $trashedTasks->total(),
                    'last_page' => $trashedTasks->lastPage(),
                    'from' => $trashedTasks->firstItem(),
                    'to' => $trashedTasks->lastItem(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve deleted tasks.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function restore(Request $request, int $id): JsonResponse
    {
        try {
            $task = Task::onlyTrashed()->where('id', $id)->where('user_id', $request->user()->id)->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found in recycle bin.',
                ], 404);
            }

            $this->authorize('restore', $task);

            $restoredTask = $this->taskService->restoreTask($id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Task restored successfully.',
                'data' => $restoredTask,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore task.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function forceDestroy(Request $request, int $id): JsonResponse
    {
        try {
            $task = Task::onlyTrashed()->where('id', $id)->where('user_id', $request->user()->id)->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found in recycle bin.',
                ], 404);
            }

            $this->authorize('forceDelete', $task);

            $this->taskService->permanentlyDeleteTask($id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Task permanently deleted.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete task.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
