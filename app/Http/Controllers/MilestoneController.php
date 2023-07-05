<?php

namespace App\Http\Controllers;

use App\Http\Requests\MilestoneRequest;
use App\Services\MilestoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MilestoneController extends Controller
{
    protected $service;

    function __construct(MilestoneService $service) {
        $this->service = $service;
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
        DB::commit();

        return redirect()->route('milestones')
            ->with("message", "マイルストーンを削除しました。");
    }
}
