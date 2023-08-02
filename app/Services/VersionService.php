<?php

namespace App\Services;

use App\Repositories\VersionRepository;
use Exception;

class VersionService
{
    private $repository;

    public function __construct(VersionRepository $repository) {
        $this->repository = $repository;
    }

    public function getVersions()
    {
        $appendQuerys = [];

        array_push($appendQuerys, function ($query) {
            $column = "version_id";
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

    public function getVersionData($data = [])
    {
        $appendQuerys = [];

        if (isset($data["version_id"])) {
            $versionId = $data["version_id"];
            array_push($appendQuerys, function ($query) use ($versionId) {
                return $this->repository->addWhereVersionIdQuery($query, $versionId);
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
        $this->updateModelById($data, $data["version_id"]);
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
