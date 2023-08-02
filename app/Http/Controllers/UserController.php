<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Providers\RouteServiceProvider;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    protected $service;

    function __construct(UserService $service) {
        $this->service = $service;
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
}
