<?php

namespace App\Services;

use App\Repositories\MilestoneRepository;
use Exception;

class MilestoneService
{
    private $repository;

    public function __construct(MilestoneRepository $repository) {
        $this->repository = $repository;
    }

    public function getMilestones()
    {
        $appendQuerys = [];

        array_push($appendQuerys, function ($query) {
            $column = "milestone_id";
            $order = "ASC";
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

    public function getMilestoneData($data = [])
    {
        $appendQuerys = [];

        if (isset($data["milestone_id"])) {
            $milestoneId = $data["milestone_id"];
            array_push($appendQuerys, function ($query) use ($milestoneId) {
                return $this->repository->addWhereMilestoneIdQuery($query, $milestoneId);
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

    public function store(array $data)
    {
        $this->repository->createModel($data);
    }

    public function update(array $data)
    {
        $this->updateModelById($data, $data["milestone_id"]);
    }

    public function updateModelById(array $params, $id)
    {
        return $this->repository->updateModelById($params, $id);
    }

    public function delete($categoryId)
    {
        $this->repository->deleteModelById($categoryId);
    }
}
