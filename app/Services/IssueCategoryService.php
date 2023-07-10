<?php

namespace App\Services;

use App\Repositories\IssueCategoryRepository;

class IssueCategoryService
{
    private $repository;

    public function __construct(IssueCategoryRepository $repository) {
        $this->repository = $repository;
    }

    public function store(array $data)
    {
        return $this->repository->createModel($data);
    }
    
    public function deleteByIssueId($issueId)
    {
        $this->repository->deleteByIssueId($issueId);
    }
}
