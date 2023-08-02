<?php

namespace App\Repositories;

use App\Models\Status;

class StatusRepository
{
    public function getModelClass()
    {
        return Status::class;
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

    public function addWhereStatusIdQuery($query, $statusId)
    {
        return $query->where('status_id', $statusId);
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
