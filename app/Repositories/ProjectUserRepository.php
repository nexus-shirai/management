<?php

namespace App\Repositories;

use App\Models\ProjectUser;

class ProjectUserRepository
{
    public function getModelClass()
    {
        return ProjectUser::class;
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

    public function deleteByProjectId($projectId)
    {
        return $this->model()->where('project_id', $projectId)->delete();
    }

    public function createModel($params)
    {
        return $this->model()->create($params);
    }

    public function addWithProjectQuery($query)
    {
        return $query->with('project');
    }

    public function addWhereUserIdQuery($query, $userId)
    {
        return $query->where('user_id', $userId);
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
