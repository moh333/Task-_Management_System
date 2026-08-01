<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Handle the incoming dashboard request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user    = $request->user();
        $metrics = $this->dashboardService->getDashboardMetrics($user->id);
        return Response::success($metrics, 'Dashboard metrics retrieved successfully');
    }
}
