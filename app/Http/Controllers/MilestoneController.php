<?php

namespace App\Http\Controllers;

use App\Http\Requests\MilestoneRequest;
use App\Services\IssueService;
use App\Services\MilestoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MilestoneController extends Controller
{
    protected $service;
    protected $issueService;

    function __construct(
        MilestoneService $service,
        IssueService $issueService
    ) {
        $this->service = $service;
        $this->issueService = $issueService;
    }

    public function index() {
        return Inertia::render("Milestones");
    }

    public function search() {
        $milestones = $this->service->getMilestones();
        return response()->json($milestones);
    }

    public function create() {
        return Inertia::render("EditMilestone", [
            "type" => "Create"
        ]);
    }

    public function store(MilestoneRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();
        $this->service->store($data);
        DB::commit();

        return redirect()->route('milestones')
            ->with("message", "マイルストーンを作成しました。");
    }

    public function edit(Request $request) {
        $data["milestone_id"] = $request->milestones_id;
        $milestoneData = $this->service->getMilestoneData($data);

        return Inertia::render("EditMilestone", [
            "type" => "Edit",
            "milestone" => $milestoneData
        ]);
    }

    public function update(MilestoneRequest $request) {
        $data = $request->validated();
        
        DB::beginTransaction();
        $this->service->update($data);
        DB::commit();

        return redirect()->route('milestones')
            ->with("message", "マイルストーンを更新しました。");
    }

    public function delete(Request $request) {
        $milestoneId = $request->milestone_id;
        
        DB::beginTransaction();
        $this->service->delete($milestoneId);

        $data['milestone_id'] = $milestoneId;
        $issues = $this->issueService->getIssues($data);
        foreach ($issues as $issue) {
            $data = [];
            $data['issue_id'] = $issue['issue_id'];
            $data['milestone_id'] = null;
            $issues = $this->issueService->updateIssue($data);
        }
        DB::commit();

        return redirect()->route('milestones')
            ->with("message", "マイルストーンを削除しました。");
    }
}
