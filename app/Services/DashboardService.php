<?php

namespace App\Services;

use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardService
{
    public function __construct(
        protected DashboardRepositoryInterface $dashboardRepository
    ) {}

    /**
     * Get aggregated dashboard metrics for a user.
     *
     * @return array<string, int>
     */
    public function getDashboardMetrics(int $userId): array
    {
        return $this->dashboardRepository->getMetrics($userId);
    }
}
