<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Providers\RouteServiceProvider;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $service;

    function __construct(UserService $service) {
        $this->service = $service;
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
        DB::commit();

        return redirect()->route('users')
            ->with("message", "ユーザーを削除しました。");
    }
}
