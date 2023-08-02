<?php

namespace App\Repositories;

use App\Models\IssueCategory;

class IssueCategoryRepository
{
    public function getModelClass()
    {
        return IssueCategory::class;
    }

    public function model()
    {
        return app($this->getModelClass());
    }

    public function get($query)
    {
        return $query->get();
    }

    public function first($query)
    {
        return $query->first();
    }

    public function deleteByIssueId($issueId)
    {
        return $this->model()->where('issue_id', $issueId)->delete();
    }

    public function deleteByCategoryId($categoryId)
    {
        return $this->model()->where('category_id', $categoryId)->delete();
    }

    public function createModel($params)
    {
        return $this->model()->create($params);
    }

    public function addWhereIssueIdQuery($query, $issueId)
    {
        return $query->where('issue_id', $issueId);
    }

    public function orderByQuery($query, $column, $order)
    {
        return $query->orderBy($column, $order);
    }

    public function getBySearchConditions(array $appendQuerys)
    {
        $query = $this->model();
        foreach ($appendQuerys as $appendQuery) {
            if (is_callable($appendQuery)) {
                $query = $appendQuery($query);
            }
        }
        return $query;
    }
}
