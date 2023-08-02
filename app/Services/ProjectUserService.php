<?php

namespace App\Services;

use App\Repositories\ProjectUserRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProjectUserService
{
    private $repository;

    public function __construct(ProjectUserRepository $repository) {
        $this->repository = $repository;
    }

    public function store(array $data)
    {
        return $this->repository->createModel($data);
    }

    public function getUserProjects()
    {
        $appendQuerys = [];

        array_push($appendQuerys, function ($query) {
            $userId = Auth::user()->user_id;
            return $this->repository->addWhereUserIdQuery($query, $userId);
        });

        array_push($appendQuerys, function ($query) {
            return $this->repository->addWithProjectQuery($query);
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

    public function deleteByProjectId($projectId)
    {
        $this->repository->deleteByProjectId($projectId);
    }
}
