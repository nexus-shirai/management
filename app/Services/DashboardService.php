<?php

namespace App\Services;

class DashboardService
{
    private $projectUserService;
    private $issueService;

    public function __construct(
        ProjectUserService $projectUserService,
        IssueService $issueService
    ) {
        $this->projectUserService = $projectUserService;
        $this->issueService = $issueService;
    }

    public function getUserProjects()
    {
        return $this->projectUserService->getUserProjects();
    }

    public function getUserIssues($data = [])
    {
        return $this->issueService->getIssues($data);
    }
}
