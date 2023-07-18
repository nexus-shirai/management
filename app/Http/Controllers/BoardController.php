<?php

namespace App\Http\Controllers;

use App\Services\BoardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class BoardController extends Controller
{
    protected $service;

    function __construct(BoardService $service) {
        $this->service = $service;
    }

    public function index(Request $request) {
        $data["project_id"] = $request->project_id;
        $project = $this->service->getProject($data);
        $data["with_kind"] = true;
        $data["with_issue_categories"] = true;
        $data["with_assignee"] = true;
        $issues = $this->service->getIssues($data);
        $statuses = $this->service->getStatuses();
        $kinds = $this->service->getKinds();
        $categories = $this->service->getCategories();
        $milestones = $this->service->getMilestones();
        $data["with_user"] = true;
        $projectUsers = $this->service->getProjectUsers($data);

        return Inertia::render("Board", [
            "project" => $project,
            "issues" => $issues,
            "statuses" => $statuses,
            "kinds" => $kinds,
            "categories" => $categories,
            "milestones" => $milestones,
            "project_users" => $projectUsers
        ]);
    }

    public function update(Request $request) {
        $data["issue_id"] = $request->issue_id;
        $data["status_id"] = $request->status_id;

        DB::beginTransaction();
            $this->service->update($data);
        DB::commit();
    }

    public function refresh(Request $request) {
        $data["project_id"] = $request->project_id;
        Session::put('lt_updated_at', $request->timestamp ? Carbon::parse($request->timestamp) : now());

        $this->service->refresh($data);
    }

    public function fetch(Request $request) {
        $data["project_id"] = $request->project_id;
        $data["with_kind"] = true;
        $data["with_issue_categories"] = true;
        $data["with_assignee"] = true;
        Session::put('lt_updated_at', now());

        $issues = $this->service->getIssues($data);

        return response()->json($issues);
    }
}
