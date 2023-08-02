<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Providers\RouteServiceProvider;
use App\Services\IssueService;
use App\Services\ProjectUserService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $service;
    protected $issueService;
    protected $projectUserService;

    function __construct(
        UserService $service,
        IssueService $issueService,
        ProjectUserService $projectUserService
    ) {
        $this->service = $service;
        $this->issueService = $issueService;
        $this->projectUserService = $projectUserService;
    }

    public function index() {
        return Inertia::render("Users");
    }

    public function search() {
        $users = $this->service->getUsers();

        return response()->json($users);
    }

    public function create() {
        return Inertia::render("EditUser", [
            "type" => "Create"
        ]);
    }

    public function store(UserRequest $request) {
        $data = $request->validated();

        DB::beginTransaction();
        $this->service->store($data);
        DB::commit();

        return redirect()->intended(RouteServiceProvider::HOME)
            ->with("message", "ユーザーを作成しました。");
    }

    public function edit(Request $request) {
        $data["user_id"] = $request->user_id;
        $userData = $this->service->getUserData($data);

        return Inertia::render("EditUser", [
            "type" => "Edit",
            "user" => $userData
        ]);
    }

    public function update(UserRequest $request) {
        $data = $request->validated();
        
        DB::beginTransaction();
        $this->service->update($data);
        DB::commit();

        return redirect()->route('users')
            ->with("message", "ユーザーを更新しました。");
    }

    public function delete(Request $request) {
        $userId = $request->user_id;

        DB::beginTransaction();
        $this->service->delete($userId);

        $this->projectUserService->deleteByUserId($userId);
        
        $data['assignee_id'] = $userId;
        $issues = $this->issueService->getIssues($data);
        foreach ($issues as $issue) {
            $data = [];
            $data['issue_id'] = $issue['issue_id'];
            $data['assignee_id'] = null;
            $issues = $this->issueService->updateIssue($data);
        }
        DB::commit();

        return redirect()->route('users')
            ->with("message", "ユーザーを削除しました。");
    }
}
