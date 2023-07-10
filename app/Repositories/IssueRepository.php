<?php

namespace App\Repositories;

use App\Models\Issue;

class IssueRepository
{
    public function getModelClass()
    {
        return Issue::class;
    }

    public function model()
    {
        return app($this->getModelClass());
    }

    public function findModelById($id)
    {
        return $this->model()->findOrFail($id);
    }

    public function updateModelById($params, $id)
    {
        return $this->findModelById($id)->fill($params)->save();
    }

    public function deleteModelById($id)
    {
        return $this->findModelById($id)->delete();
    }

    public function get($query)
    {
        return $query->get();
    }

    public function first($query)
    {
        return $query->first();
    }

    public function count($query)
    {
        return $query->count();
    }

    public function createModel($params)
    {
        return $this->model()->create($params);
    }

    public function orderByQuery($query, $column, $order)
    {
        return $query->orderBy($column, $order);
    }

    public function addWhereIssueIdQuery($query, $issueId)
    {
        return $query->where('issue_id', $issueId);
    }

    public function addWhereStatusIdQuery($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    public function addWhereProjectIdQuery($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function addWhereNotIssueIdQuery($query, $issueId)
    {
        return $query->where('issue_id', '<>', $issueId);
    }

    public function addWithKindQuery($query)
    {
        return $query->with('kind');
    }

    public function addWithIssueCategoriesQuery($query)
    {
        return $query->with('issue_categories');
    }

    public function addWithStatusQuery($query)
    {
        return $query->with('status');
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
