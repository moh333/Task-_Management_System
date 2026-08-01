<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Project\StoreProjectRequest;
use App\Http\Requests\Api\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    /**
     * Display a listing of the user's projects with pagination and Resource transformation.
     */
    public function index(Request $request): JsonResponse
    {
        $user              = $request->user();
        $perPage           = (int) $request->query('per_page', 10);
        $paginatedProjects = $this->projectService->getPaginatedUserProjects($user->id, $perPage);
        return Response::success(ProjectResource::collection($paginatedProjects)->response()->getData(true), 'Projects retrieved successfully');
    }

    /**
     * Store a newly created project in storage using ProjectResource.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $user    = $request->user();
        $project = $this->projectService->createProject($user->id, $request->validated());
        return Response::success(new ProjectResource($project), 'Project created successfully', 201);
    }

    /**
     * Display the specified project using ProjectResource.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user    = $request->user();
        $project = $this->projectService->getUserProject($user->id, $id);
        if (!$project) {
            return Response::notFound('Project not found');
        }
        return Response::success(new ProjectResource($project), 'Project retrieved successfully');
    }

    /**
     * Update the specified project in storage using ProjectResource.
     */
    public function update(UpdateProjectRequest $request, int $id): JsonResponse
    {
        $user           = $request->user();
        $project        = $this->projectService->getUserProject($user->id, $id);
        if (!$project) {
            return Response::notFound('Project not found');
        }
        $updatedProject = $this->projectService->updateProject($project, $request->validated());
        return Response::success(new ProjectResource($updatedProject), 'Project updated successfully');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user    = $request->user();
        $project = $this->projectService->getUserProject($user->id, $id);
        if (!$project) {
            return Response::notFound('Project not found');
        }
        $this->projectService->deleteProject($project);
        return Response::success(null, 'Project deleted successfully');
    }
}
