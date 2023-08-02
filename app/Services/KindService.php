<?php

namespace App\Services;

use App\Repositories\KindRepository;
use Exception;

class KindService
{
    private $repository;

    public function __construct(KindRepository $repository) {
        $this->repository = $repository;
    }

    public function getKinds()
    {
        $appendQuerys = [];

        array_push($appendQuerys, function ($query) {
            return $this->repository->get($query);
        });

        try {
            return $this->repository->getBySearchConditions($appendQuerys);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getKindData($data = [])
    {
        $appendQuerys = [];

        if (isset($data["kind_id"])) {
            $kindId = $data["kind_id"];
            array_push($appendQuerys, function ($query) use ($kindId) {
                return $this->repository->addWhereKindIdQuery($query, $kindId);
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
        $this->updateModelById($data, $data["kind_id"]);
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
