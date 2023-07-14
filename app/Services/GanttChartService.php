<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GanttChartService
{
    private $issueService;
    private $statusService;
    private $projectService;

    public function __construct(
        IssueService $issueService,
        StatusService $statusService,
        ProjectService $projectService
    ) {
        $this->issueService = $issueService;
        $this->statusService = $statusService;
        $this->projectService = $projectService;
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

    public function update($data) {
        $this->issueService->updatePeriod($data);
    }

    public function refresh($data) {
        $response = new StreamedResponse(function () use ($data) {
            while (true) {
                $data['lt_updated_at'] = Session::get('lt_updated_at');
                $new_data = $this->issueService->getIssues($data);
                $refresh = [ 'refresh' => count($new_data) > 0 ];

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
