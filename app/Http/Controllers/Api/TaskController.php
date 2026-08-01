<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Task\StoreTaskRequest;
use App\Http\Requests\Api\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks with filtering, search, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $user      = $request->user();
        $perPage   = (int) $request->query('per_page', 10);
        $projectId = $request->query('project_id') ? (int) $request->query('project_id') : null;
        $filters = [
            'status'   => $request->query('status'),
            'priority' => $request->query('priority'),
            'search'   => $request->query('search') ?? $request->query('title'),
        ];
        $paginatedTasks = $this->taskService->getPaginatedUserTasks($user->id, $projectId, array_filter($filters), $perPage);
        if ($paginatedTasks === null) {
            return Response::notFound('Project not found');
        }
        return Response::success(TaskResource::collection($paginatedTasks)->response()->getData(true), 'Tasks retrieved successfully');
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $user = $request->user();
        $task = $this->taskService->createTask($user->id, $request->validated());
        return Response::success(new TaskResource($task), 'Task created successfully', 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $task = $this->taskService->getTaskForUser($user->id, $id);
        if (!$task) {
            return Response::notFound('Task not found');
        }
        return Response::success(new TaskResource($task), 'Task retrieved successfully');
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $user = $request->user();
        $task = $this->taskService->getTaskForUser($user->id, $id);
        if (!$task) {
            return Response::notFound('Task not found');
        }
        $updatedTask = $this->taskService->updateTask($task, $request->validated());
        return Response::success(new TaskResource($updatedTask), 'Task updated successfully');
    }

    /**
     * Remove the specified task from storage (Soft Delete).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $task = $this->taskService->getTaskForUser($user->id, $id);
        if (!$task) {
            return Response::notFound('Task not found');
        }
        $this->taskService->deleteTask($task);
        return Response::success(null, 'Task deleted successfully');
    }
}
