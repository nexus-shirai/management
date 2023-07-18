<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BoardService
{
    private $issueService;
    private $statusService;
    private $projectService;
    private $kindService;
    private $categoryService;
    private $milestoneService;
    private $projectUserService;

    public function __construct(
        IssueService $issueService,
        StatusService $statusService,
        ProjectService $projectService,
        KindService $kindService,
        CategoryService $categoryService,
        MilestoneService $milestoneService,
        ProjectUserService $projectUserService
    ) {
        $this->issueService = $issueService;
        $this->statusService = $statusService;
        $this->projectService = $projectService;
        $this->kindService = $kindService;
        $this->categoryService = $categoryService;
        $this->milestoneService = $milestoneService;
        $this->projectUserService = $projectUserService;
    }

    public function getIssues($data = [])
    {
        return $this->issueService->getIssues($data);
    }

    public function getStatuses() {
        return $this->statusService->getStatuses();
    }

    public function getProject($data = [])
    {
        return $this->projectService->getProjectData($data);
    }

    public function getKinds() {
        return $this->kindService->getKinds();
    }

    public function getCategories() {
        return $this->categoryService->getCategories();
    }

    public function getMilestones() {
        return $this->milestoneService->getMilestones();
    }

    public function getProjectUsers($data) {
        return $this->projectUserService->getProjectUsers($data);
    }

    public function update($data) {
        $this->issueService->updateIssue($data);
    }

    public function refresh($data) {
        $response = new StreamedResponse(function () use ($data) {
            while (true) {
                $data['lt_updated_at'] = Session::get('lt_updated_at');
                $new_data = $this->issueService->getIssues($data);
                $refresh = [
                    'refresh' => count($new_data) > 0,
                    'timestamp' => Carbon::now()->format("Y-m-d H:i:s"),
                ];

                echo 'data: ' . json_encode($refresh) . "\n\n";
                ob_flush();
                flush();
                usleep(200000);
            }
        });
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cach-Control', 'no-cache');
        $response->send();
    }
}
