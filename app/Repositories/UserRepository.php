<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getModelClass()
    {
        return User::class;
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

    public function createModel($params)
    {
        return $this->model()->create($params);
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

    public function orderByQuery($query, $column, $order)
    {
        return $query->orderBy($column, $order);
    }

    public function addWhereUserIdQuery($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function addWhereUserTypeQuery($query, $userType)
    {
        return $query->where('user_type', $userType);
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
