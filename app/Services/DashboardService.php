<?php

namespace App\Services;

class DashboardService
{
    private $projectUserService;

    public function __construct(ProjectUserService $projectUserService) {
        $this->projectUserService = $projectUserService;
    }

    public function getUserProjects()
    {
        return $this->projectUserService->getUserProjects();
    }
}
