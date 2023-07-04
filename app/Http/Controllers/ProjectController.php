<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Providers\RouteServiceProvider;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProjectController extends Controller
{
    protected $service;

    function __construct(ProjectService $service) {
        $this->service = $service;
    }

    public function view(Request $request) {
        $data["project_id"] = $request->project_id;
        $data["with_project_users"] = true;
        $projectData = $this->service->getProjectData($data);
        $users = $this->service->getGeneralUsers();

        return Inertia::render("EditProject", [
            "type" => "View",
            "project" => $projectData,
            "users" => $users
        ]);
    }

    public function edit(Request $request) {
        $data["project_id"] = $request->project_id;
        $data["with_project_users"] = true;
        $projectData = $this->service->getProjectData($data);
        $users = $this->service->getGeneralUsers();

        return Inertia::render("EditProject", [
            "type" => "Edit",
            "project" => $projectData,
            "users" => $users
        ]);
    }

    public function update(ProjectRequest $request) {
        $data = $request->validated();
        
        DB::beginTransaction();
        $this->service->update($data);
        DB::commit();

        return redirect()->intended(route('view-project', ['project_id' => $data['project_id']]))
            ->with("message", "プロジェクトを更新しました。");
    }

    public function delete(Request $request) {
        $projectId = $request->project_id;
        DB::beginTransaction();
        $this->service->delete($projectId);
        DB::commit();

        return redirect()->intended(RouteServiceProvider::HOME)
            ->with("message", "プロジェクトを削除しました。");
    }

    public function create() {
        $users = $this->service->getGeneralUsers();
        return Inertia::render("EditProject", [
            "type" => "Create",
            "users" => $users
        ]);
    }

    public function store(ProjectRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();
        $this->service->store($data);
        DB::commit();

        return redirect()->intended(RouteServiceProvider::HOME)
            ->with("message", "プロジェクトを作成しました。");
    }
}
