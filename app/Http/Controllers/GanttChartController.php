<?php

namespace App\Http\Controllers;

use App\Services\GanttChartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class GanttChartController extends Controller
{
    protected $service;

    function __construct(GanttChartService $service) {
        $this->service = $service;
    }

    public function index(Request $request) {
        $data["project_id"] = $request->project_id;
        $project = $this->service->getProject($data);
        $data["with_kind"] = true;
        $data["with_status"] = true;
        $data["with_issue_categories"] = true;
        $data["with_assignee"] = true;
        $data["with_milestone"] = true;
        $data["with_child_issues"] = true;
        $data["with_parent_issue"] = true;
        $issues = $this->service->getIssues($data);
        $statuses = $this->service->getStatuses();

        return Inertia::render("GanttChart", [
            "project" => $project,
            "issues" => $issues,
            "statuses" => $statuses,
        ]);
    }

    public function update(Request $request) {
        $issues = $request->issues;

        DB::beginTransaction();
        foreach($issues as $issue) {
            $this->service->update($issue);
        }
        DB::commit();
        session_start();
        $_SESSION["lt_updated_at"] = now();
        Session::put('lt_updated_at', now());
    }

    public function refresh(Request $request) {
        $data["project_id"] = $request->project_id;
        Session::put('lt_updated_at', now());

        $this->service->refresh($data);
    }

    public function fetch(Request $request) {
        $data["project_id"] = $request->project_id;
        $data["with_kind"] = true;
        $data["with_status"] = true;
        $data["with_issue_categories"] = true;
        $data["with_assignee"] = true;
        $data["with_milestone"] = true;
        $data["with_child_issues"] = true;
        $data["with_parent_issue"] = true;
        Session::put('lt_updated_at', now());

        $issues = $this->service->getIssues($data);

        return response()->json($issues);
    }
}
