<?php

namespace App\Http\Controllers;

use App\Http\Requests\IssueRequest;
use App\Services\IssueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class IssueController extends Controller
{
    protected $service;

    function __construct(IssueService $service) {
        $this->service = $service;
    }

    public function index(Request $request) {
        $data["project_id"] = $request->project_id;
        $projectData = $this->service->getProjectData($data);
        $data["with_kind"] = true;
        $data["with_status"] = true;
        $data["with_issue_categories"] = true;
        $issues = $this->service->getIssues($data);
        $statuses = $this->service->getStatuses();
        $categories = $this->service->getCategories();
        $milestones = $this->service->getMilestones();
        $data["with_user"] = true;
        $project_users = $this->service->getProjectUsers($data);

        return Inertia::render("Issues", [
            "project" => $projectData,
            "issues" => $issues,
            "statuses" => $statuses,
            "categories" => $categories,
            "milestones" => $milestones,
            "project_users" => $project_users,
        ]);
    }

    public function create(Request $request) {
        $data["project_id"] = $request->project_id;
        $project = $this->service->getProjectData($data);
        $kinds = $this->service->getKinds();
        $categories = $this->service->getCategories();
        $statuses = $this->service->getStatuses();
        $data["with_user"] = true;
        $project_users = $this->service->getProjectUsers($data);
        $milestones = $this->service->getMilestones();
        $versions = $this->service->getVersions();

        $data = [];
        $data["project_id"] = $request->project_id;
        $data["where_not_issue_id"] = $request->issue_id;
        $issues = $this->service->getIssues($data);

        return Inertia::render("EditIssue", [
            "type" => "Create",
            "project" => $project,
            "kinds" => $kinds,
            "categories" => $categories,
            "statuses" => $statuses,
            "project_users" => $project_users,
            "milestones" => $milestones,
            "versions" => $versions,
            "issues" => $issues,
        ]);
    }

    public function store(IssueRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();
        $this->service->store($data);
        DB::commit();

        return redirect()->route("issues", ['project_id' => $request->project_id])
            ->with("message", "課題を作成しました。");
    }

    public function edit(Request $request) {
        $data["project_id"] = $request->project_id;
        $project = $this->service->getProjectData($data);
        $data["issue_id"] = $request->issue_id;
        $data["with_issue_categories"] = true;
        $issue = $this->service->getIssueData($data);
        $kinds = $this->service->getKinds();
        $categories = $this->service->getCategories();
        $statuses = $this->service->getStatuses();
        $data["with_user"] = true;
        $project_users = $this->service->getProjectUsers($data);
        $milestones = $this->service->getMilestones();
        $versions = $this->service->getVersions();

        $data = [];
        $data["project_id"] = $request->project_id;
        $data["where_not_issue_id"] = $request->issue_id;
        $issues = $this->service->getIssues($data);

        return Inertia::render("EditIssue", [
            "type" => "Edit",
            "project" => $project,
            "issue" => $issue,
            "kinds" => $kinds,
            "categories" => $categories,
            "statuses" => $statuses,
            "project_users" => $project_users,
            "milestones" => $milestones,
            "versions" => $versions,
            "issues" => $issues,
        ]);
    }

    public function update(IssueRequest $request) {
        $data = $request->validated();
        
        DB::beginTransaction();
        $this->service->update($data);
        DB::commit();

        return redirect()->route('view-issue', [
                'project_id' => $data['project_id'],
                'issue_id' => $data['issue_id']
            ])
            ->with("message", "課題を更新しました。");
    }

    public function view(Request $request) {
        $data["project_id"] = $request->project_id;
        $project = $this->service->getProjectData($data);
        $data["issue_id"] = $request->issue_id;
        $data["with_issue_categories"] = true;
        $issue = $this->service->getIssueData($data);
        $kinds = $this->service->getKinds();
        $categories = $this->service->getCategories();
        $statuses = $this->service->getStatuses();
        $data["with_user"] = true;
        $project_users = $this->service->getProjectUsers($data);
        $milestones = $this->service->getMilestones();
        $versions = $this->service->getVersions();

        $data = [];
        $data["project_id"] = $request->project_id;
        $data["where_not_issue_id"] = $request->issue_id;
        $issues = $this->service->getIssues($data);

        return Inertia::render("EditIssue", [
            "type" => "View",
            "project" => $project,
            "issue" => $issue,
            "kinds" => $kinds,
            "categories" => $categories,
            "statuses" => $statuses,
            "project_users" => $project_users,
            "milestones" => $milestones,
            "versions" => $versions,
            "issues" => $issues,
        ]);
    }

    public function delete(Request $request) {
        $issueId = $request->issue_id;
        DB::beginTransaction();
        $this->service->delete($issueId);
        DB::commit();

        return redirect()->route("issues", ['project_id' => $request->project_id])
            ->with("message", "課題を削除しました。");
    }
}
