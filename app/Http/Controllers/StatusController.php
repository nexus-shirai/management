<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusRequest;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatusController extends Controller
{
    protected $service;

    function __construct(StatusService $service) {
        $this->service = $service;
    }

    public function index() {
        return Inertia::render("Statuses");
    }

    public function search() {
        $milestones = $this->service->getStatuses();

        return response()->json($milestones);
    }

    public function create() {
        return Inertia::render("EditStatus", [
            "type" => "Create"
        ]);
    }

    public function store(StatusRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();
        $this->service->store($data);
        DB::commit();

        return redirect()->route('statuses')
            ->with("message", "状態を作成しました。");
    }

    public function edit(Request $request) {
        $data["status_id"] = $request->status_id;
        $statusData = $this->service->getStatusData($data);

        return Inertia::render("EditStatus", [
            "type" => "Edit",
            "status" => $statusData
        ]);
    }

    public function update(StatusRequest $request) {
        $data = $request->validated();
        
        DB::beginTransaction();
        $this->service->update($data);
        DB::commit();

        return redirect()->route('statuses')
            ->with("message", "状態を更新しました。");
    }

    public function delete(Request $request) {
        $statusId = $request->status_id;
        DB::beginTransaction();
        $this->service->delete($statusId);
        DB::commit();

        return redirect()->route('statuses')
            ->with("message", "状態を削除しました。");
    }
}
