<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function getUsers($data = [])
    {
        $appendQuerys = [];

        if (isset($data["user_type"])) {
            $userType = $data["user_type"];
            array_push($appendQuerys, function ($query) use ($userType) {
                return $this->repository->addWhereUserTypeQuery($query, $userType);
            });
        }

        array_push($appendQuerys, function ($query) {
            return $this->repository->get($query);
        });

        try {
            return $this->repository->getBySearchConditions($appendQuerys);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getCategories()
    {
        $appendQuerys = [];

        array_push($appendQuerys, function ($query) {
            return $this->repository->orderByQuery($query, "category_id", "ASC");
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
