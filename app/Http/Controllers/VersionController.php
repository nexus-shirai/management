<?php

namespace App\Http\Controllers;

use App\Http\Requests\VersionRequest;
use App\Services\IssueService;
use App\Services\VersionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VersionController extends Controller
{
    protected $service;
    protected $issueService;

    function __construct(
        VersionService $service,
        IssueService $issueService
    ) {
        $this->service = $service;
        $this->issueService = $issueService;
    }

    public function index() {
        return Inertia::render("Versions");
    }

    public function search() {
        $milestones = $this->service->getVersions();

        return response()->json($milestones);
    }

    public function create() {
        return Inertia::render("EditVersion", [
            "type" => "Create"
        ]);
    }

    public function store(VersionRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();
        $this->service->store($data);
        DB::commit();

        return redirect()->route('versions')
            ->with("message", "バージョンを作成しました。");
    }

    public function edit(Request $request) {
        $data["version_id"] = $request->version_id;
        $versionData = $this->service->getVersionData($data);

        return Inertia::render("EditVersion", [
            "type" => "Edit",
            "version" => $versionData
        ]);
    }

    public function update(VersionRequest $request) {
        $data = $request->validated();
        
        DB::beginTransaction();
        $this->service->update($data);
        DB::commit();

        return redirect()->route('versions')
            ->with("message", "バージョンを更新しました。");
    }

    public function delete(Request $request) {
        $versionId = $request->version_id;

        DB::beginTransaction();
        $this->service->delete($versionId);

        $data['version_id'] = $versionId;
        $issues = $this->issueService->getIssues($data);
        foreach ($issues as $issue) {
            $data = [];
            $data['issue_id'] = $issue['issue_id'];
            $data['version_id'] = null;
            $issues = $this->issueService->updateIssue($data);
        }
        DB::commit();

        return redirect()->route('versions')
            ->with("message", "バージョンを削除しました。");
    }
}
