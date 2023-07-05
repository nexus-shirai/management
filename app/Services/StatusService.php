<?php

namespace App\Services;

use App\Repositories\StatusRepository;
use Exception;

class StatusService
{
    private $repository;

    public function __construct(StatusRepository $repository) {
        $this->repository = $repository;
    }

    public function getStatuses()
    {
        $appendQuerys = [];

        array_push($appendQuerys, function ($query) {
            $column = "status_id";
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

    public function getStatusData($data = [])
    {
        $appendQuerys = [];

        if (isset($data["status_id"])) {
            $statusId = $data["status_id"];
            array_push($appendQuerys, function ($query) use ($statusId) {
                return $this->repository->addWhereStatusIdQuery($query, $statusId);
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
        $this->updateModelById($data, $data["status_id"]);
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
