<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $service;

    function __construct(DashboardService $service) {
        $this->service = $service;
    }

    public function index() {
        $userProjects = $this->service->getUserProjects();
        $data["with_kind"] = true;
        $data["with_status"] = true;
        $data["assignee_or_create_user_id"] = Auth::user()->user_id;
        $userIssues = $this->service->getUserIssues($data);
        
        return Inertia::render("Dashboard", [
            "user_projects" => $userProjects,
            "user_issues" => $userIssues
        ]);
    }
}
