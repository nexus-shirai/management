<?php

namespace App\Http\Controllers;

use App\Http\Requests\KindRequest;
use App\Services\KindService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KindController extends Controller
{
    protected $service;

    function __construct(KindService $service) {
        $this->service = $service;
    }

    public function index() {
        return Inertia::render("Kinds");
    }

    public function search() {
        $milestones = $this->service->getKinds();

        return response()->json($milestones);
    }

    public function create() {
        return Inertia::render("EditKind", [
            "type" => "Create"
        ]);
    }

    public function store(KindRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();
        $this->service->store($data);
        DB::commit();

        return redirect()->route('kinds')
            ->with("message", "種別を作成しました。");
    }

    public function edit(Request $request) {
        $data["kind_id"] = $request->kind_id;
        $kindData = $this->service->getKindData($data);

        return Inertia::render("EditKind", [
            "type" => "Edit",
            "kind" => $kindData
        ]);
    }

    public function update(KindRequest $request) {
        $data = $request->validated();
        
        DB::beginTransaction();
        $this->service->update($data);
        DB::commit();

        return redirect()->route('kinds')
            ->with("message", "種別を更新しました。");
    }

    public function delete(Request $request) {
        $kindId = $request->kind_id;
        DB::beginTransaction();
        $this->service->delete($kindId);
        DB::commit();

        return redirect()->route('kinds')
            ->with("message", "種別を削除しました。");
    }
}
