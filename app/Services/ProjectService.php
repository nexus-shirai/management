<?php

namespace App\Services;

use App\Repositories\ProjectRepository;
use Exception;

class ProjectService
{
    private $repository;
    private $userService;
    private $projectUserService;

    public function __construct(
        ProjectRepository $repository,
        UserService $userService,
        ProjectUserService $projectUserService
    ) {
        $this->repository = $repository;
        $this->userService = $userService;
        $this->projectUserService = $projectUserService;
    }

    public function store(array $data)
    {
        $project = $this->repository->createModel($data);
        
        foreach ($data["project_users"] as $project_user) {
            $projectUserData = [
                "project_id" => $project["project_id"],
                "user_id" => $project_user
            ];
            $this->projectUserService->store($projectUserData);
        }
    }

    public function update(array $data)
    {
        $this->updateModelById($data, $data["project_id"]);

        $this->deleteByProjectId($data["project_id"]);
        foreach ($data["project_users"] as $project_user) {
            $projectUserData = [
                "project_id" => $data["project_id"],
                "user_id" => $project_user
            ];
            $this->projectUserService->store($projectUserData);
        }
    }

    public function delete($projectId)
    {
        $this->repository->deleteModelById($projectId);
        $this->projectUserService->deleteByProjectId($projectId);
    }
    
    public function getGeneralUsers()
    {
        return $this->userService->getGeneralUsers();
    }

    public function updateModelById(array $params, $id)
    {
        return $this->repository->updateModelById($params, $id);
    }

    private function deleteByProjectId($projectId)
    {
        $this->projectUserService->deleteByProjectId($projectId);
    }

    public function getProjects()
    {
        $appendQuerys = [];

        array_push($appendQuerys, function ($query) {
            $column = "project_id";
            $order = "DESC";
            return $this->repository->orderByQuery($query, $column, $order);
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

    public function getProjectData($data = [])
    {
        $appendQuerys = [];

        if (isset($data["project_id"])) {
            $projectId = $data["project_id"];
            array_push($appendQuerys, function ($query) use ($projectId) {
                return $this->repository->addWhereProjectIdQuery($query, $projectId);
            });
        }

        if (isset($data["with_project_users"]) && $data["with_project_users"]) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithProjectUsersQuery($query);
            });
        }

        array_push($appendQuerys, function ($query) {
            return $this->repository->first($query);
        });

        try {
            return $this->repository->getBySearchConditions($appendQuerys);
        } catch (Exception $e) {
            return $e;
        }
    }
}
