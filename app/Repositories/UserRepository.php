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

    public function createModel($params)
    {
        return $this->model()->create($params);
    }
}
