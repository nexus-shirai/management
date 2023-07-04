<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $service;

    function __construct(DashboardService $service) {
        $this->service = $service;
    }

    public function index() {
        $userProjects = $this->service->getUserProjects();
        return Inertia::render("Dashboard", [
            "user_projects" => $userProjects
        ]);
    }
}
