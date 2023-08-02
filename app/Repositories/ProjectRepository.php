<?php

namespace App\Repositories;

use App\Models\Project;

class ProjectRepository
{
    public function getModelClass()
    {
        return Project::class;
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

    public function createModel($params)
    {
        return $this->model()->create($params);
    }

    public function orderByQuery($query, $column, $order)
    {
        return $query->orderBy($column, $order);
    }

    public function addWhereProjectIdQuery($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function addWithProjectUsersQuery($query) {
        return $query->with('project_users');
    }

    public function addWithIssuesQuery($query) {
        return $query->with('issues');
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
