<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function getGeneralUsers()
    {
        $appendQuerys = [];

        array_push($appendQuerys, function ($query) {
            $column = "user_id";
            $order = "ASC";
            return $this->repository->orderByQuery($query, $column, $order);
        });

        array_push($appendQuerys, function ($query) {
            $userType = User::USER_TYPE_GENERAL;
            return $this->repository->addWhereUserTypeQuery($query, $userType);
        });

        array_push($appendQuerys, function ($query) {
            return $this->repository->get($query);
        });

        try {
            return $this->repository->getBySearchConditions($appendQuerys);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function store(array $data)
    {
        $data["password"] = Hash::make($data["password"]);
        return $this->repository->createModel($data);
    }
}
