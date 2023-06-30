<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function store(array $data)
    {
        $data["password"] = Hash::make($data["password"]);
        return $this->repository->createModel($data);
    }
}
