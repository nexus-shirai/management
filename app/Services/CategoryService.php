<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Exception;

class CategoryService
{
    private $repository;

    public function __construct(CategoryRepository $repository) {
        $this->repository = $repository;
    }

    public function getCategories()
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

    public function getCategoryData($data = [])
    {
        $appendQuerys = [];

        if (isset($data["category_id"])) {
            $categoryId = $data["category_id"];
            array_push($appendQuerys, function ($query) use ($categoryId) {
                return $this->repository->addWhereCategoryIdQuery($query, $categoryId);
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
        $this->updateModelById($data, $data["category_id"]);
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
