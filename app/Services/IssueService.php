<?php

namespace App\Services;

use App\Repositories\IssueRepository;
use Exception;

class IssueService
{
    private $repository;
    private $projectService;
    private $kindService;
    private $categoryService;
    private $statusService;
    private $projectUserService;
    private $milestoneService;
    private $versionService;
    private $issueCategoryService;

    public function __construct(
        IssueRepository $repository,
        ProjectService $projectService,
        KindService $kindService,
        CategoryService $categoryService,
        StatusService $statusService,
        ProjectUserService $projectUserService,
        MilestoneService $milestoneService,
        VersionService $versionService,
        IssueCategoryService $issueCategoryService
    ) {
        $this->repository = $repository;
        $this->projectService = $projectService;
        $this->kindService = $kindService;
        $this->categoryService = $categoryService;
        $this->statusService = $statusService;
        $this->projectUserService = $projectUserService;
        $this->milestoneService = $milestoneService;
        $this->versionService = $versionService;
        $this->issueCategoryService = $issueCategoryService;
    }

    public function getIssues($data = [])
    {
        $appendQuerys = [];

        if (isset($data["project_id"])) {
            $projectId = $data["project_id"];
            array_push($appendQuerys, function ($query) use ($projectId) {
                return $this->repository->addWhereProjectIdQuery($query, $projectId);
            });
        }
        
        if (isset($data["where_not_issue_id"])) {
            $issueId = $data["where_not_issue_id"];
            array_push($appendQuerys, function ($query) use ($issueId) {
                return $this->repository->addWhereNotIssueIdQuery($query, $issueId);
            });
        }

        if (isset($data["with_kind"]) && $data["with_kind"]) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithKindQuery($query);
            });
        }

        if (isset($data["with_status"]) && $data["with_status"]) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithStatusQuery($query);
            });
        }

        if (isset($data["with_issue_categories"]) && $data["with_issue_categories"]) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithIssueCategoriesQuery($query);
            });
        }

        if (isset($data["assignee_or_create_user_id"])) {
            $assigneeOrCreateUserId = $data["assignee_or_create_user_id"];
            array_push($appendQuerys, function ($query) use($assigneeOrCreateUserId) {
                return $this->repository->addWhereAssigneeOrCreateUserId($query, $assigneeOrCreateUserId);
            });
        }

        array_push($appendQuerys, function ($query) {
            $column = "issue_id";
            $order = "DESC";
            return $this->repository->orderByQuery($query, $column, $order);
        });

        array_push($appendQuerys, function ($query) {
            return $this->repository->get($query);
        });

        try {
            return $this->repository->getBySearchConditions($appendQuerys);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getIssueData($data = [])
    {
        $appendQuerys = [];

        if (isset($data["issue_id"])) {
            $issueId = $data["issue_id"];
            array_push($appendQuerys, function ($query) use ($issueId) {
                return $this->repository->addWhereIssueIdQuery($query, $issueId);
            });
        }

        if (isset($data["with_issue_categories"]) && $data["with_issue_categories"]) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithIssueCategoriesQuery($query);
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

    public function getProjectData($data = [])
    {
        return $this->projectService->getProjectData($data);
    }

    public function getKinds() {
        return $this->kindService->getKinds();
    }

    public function getCategories() {
        return $this->categoryService->getCategories();
    }

    public function getStatuses() {
        return $this->statusService->getStatuses();
    }

    public function getProjectUsers($data) {
        return $this->projectUserService->getProjectUsers($data);
    }

    public function getMilestones() {
        return $this->milestoneService->getMilestones();
    }

    public function getVersions() {
        return $this->versionService->getVersions();
    }

    public function store(array $data)
    {
        $data["issue_cd"] = $this->generateIssueCode($data["project_id"]);
        $this->repository->createModel($data);
    }

    public function updateModelById(array $params, $id)
    {
        return $this->repository->updateModelById($params, $id);
    }

    public function update(array $data)
    {
        $this->updateModelById($data, $data["issue_id"]);

        $this->deleteByIssueId($data["issue_id"]);
        foreach ($data["issue_categories"] as $issue_category) {
            $issueCategoryData = [
                "issue_id" => $data["issue_id"],
                "category_id" => $issue_category
            ];
            $this->issueCategoryService->store($issueCategoryData);
        }
    }

    private function deleteByIssueId($issueId)
    {
        $this->issueCategoryService->deleteByIssueId($issueId);
    }

    private function generateIssueCode($project_id) {
        $params["project_id"] = $project_id;
        $projectData = $this->getProjectData($params);
        $issue_cd = $projectData["project_cd"] . "_" . ($this->countProjectIssues($params) + 1);
        return $issue_cd;
    }

    private function countProjectIssues($data)
    {
        $appendQuerys = [];

        if (isset($data["project_id"])) {
            $projectId = $data["project_id"];
            array_push($appendQuerys, function ($query) use ($projectId) {
                return $this->repository->addWhereProjectIdQuery($query, $projectId);
            });
        }

        array_push($appendQuerys, function ($query) {
            return $this->repository->count($query);
        });

        try {
            return $this->repository->getBySearchConditions($appendQuerys);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function delete($issueId)
    {
        $this->repository->deleteModelById($issueId);
    }
}
