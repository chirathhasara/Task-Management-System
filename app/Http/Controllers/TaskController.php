<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
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

    public function index(Request $request): JsonResponse
    {
        try {
            $status = $request->query('status');
            
            if ($status && in_array($status, ['pending', 'completed'])) {
                $tasks = $this->taskService->getTasksByStatus($request->user(), $status);
            } else {
                $tasks = $this->taskService->getAllTasks($request->user());
            }

            return response()->json([
                'success' => true,
                'data' => $tasks,
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

            if (!$this->taskService->verifyTaskOwnership($task, $request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this task.',
                ], 403);
            }

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

            if (!$this->taskService->verifyTaskOwnership($task, $request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this task.',
                ], 403);
            }

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

            if (!$this->taskService->verifyTaskOwnership($task, $request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this task.',
                ], 403);
            }

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

            if (!$this->taskService->verifyTaskOwnership($task, $request->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this task.',
                ], 403);
            }

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
}
