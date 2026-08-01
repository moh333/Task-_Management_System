<?php

namespace App\Repositories\Contracts;

interface DashboardRepositoryInterface
{
    /**
     * Get aggregated dashboard metrics for a user using concurrency pool.
     *
     * @return array<string, int>
     */
    public function getMetrics(int $userId): array;
}
