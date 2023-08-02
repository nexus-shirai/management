<?php

namespace App\Services;

use App\Repositories\IssueRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

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

        if (isset($data["assignee_id"])) {
            $assigneeId = $data["assignee_id"];
            array_push($appendQuerys, function ($query) use ($assigneeId) {
                return $this->repository->addWhereAssigneeIdQuery($query, $assigneeId);
            });
        }
        
        if (isset($data["where_not_issue_id"])) {
            $issueId = $data["where_not_issue_id"];
            array_push($appendQuerys, function ($query) use ($issueId) {
                return $this->repository->addWhereNotIssueIdQuery($query, $issueId);
            });
        }

        if (isset($data["milestone_id"])) {
            $milestoneId = $data["milestone_id"];
            array_push($appendQuerys, function ($query) use ($milestoneId) {
                return $this->repository->addWhereMilestoneIdQuery($query, $milestoneId);
            });
        }

        if (isset($data["version_id"])) {
            $versionId = $data["version_id"];
            array_push($appendQuerys, function ($query) use ($versionId) {
                return $this->repository->addWhereVersionIdQuery($query, $versionId);
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

        if (isset($data["with_assignee"]) && $data["with_assignee"]) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithAssigneeQuery($query);
            });
        }

        if (isset($data["with_child_issues"])) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithChildIssues($query);
            });
        }

        if (isset($data["with_parent_issue"])) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithParentIssue($query);
            });
        }

        if (isset($data["with_milestone"])) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithMilestone($query);
            });
        }

        if (isset($data["assignee_or_create_user_id"])) {
            $assigneeOrCreateUserId = $data["assignee_or_create_user_id"];
            array_push($appendQuerys, function ($query) use($assigneeOrCreateUserId) {
                return $this->repository->addWhereAssigneeOrCreateUserId($query, $assigneeOrCreateUserId);
            });
        }

        if (isset($data["lt_updated_at"])) {
            $timestamp = $data["lt_updated_at"];
            array_push($appendQuerys, function ($query) use($timestamp) {
                return $this->repository->addWhereLesserThanUpdatedAt($query, $timestamp);
            });
        }

        array_push($appendQuerys, function ($query) {
            return $this->repository->get($query);
        });

        try {
            return $this->repository->getBySearchConditions($appendQuerys);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function updateChildIssueTimeline($data) {
        $params['issue_id'] = $data['issue_id'];
        $params['with_child_issues'] = true;
        $issue = $this->getIssueData($params);

        $start_date = Carbon::parse($data['start_date']);
        $end_date = Carbon::parse($data['end_date']);

        if ($issue['child_issues']) {
            foreach ($issue['child_issues'] as $child_issue) {
                $child_start_date = Carbon::parse($child_issue['start_date']);
                $child_end_date = Carbon::parse($child_issue['end_date']);

                if ($start_date->gt($child_start_date) || $end_date->lt($child_end_date)) {
                    $diff = $start_date->diffInDays($child_start_date);

                    if ($start_date->lt($child_start_date)) {
                        $child_start_date->subDays($diff);
                        $child_end_date->subDays($diff);
                    } else {
                        $child_start_date->addDays($diff);
                        $child_end_date->addDays($diff);
                    }

                    if ($diff > 0 || $child_end_date->isAfter($end_date)) {
                        if ($child_end_date->isAfter($end_date)) {
                            $child_end_date = $end_date;
                        }

                        $data = [];
                        $data['issue_id'] = $child_issue['issue_id'];
                        $data['start_date'] = $child_start_date;
                        $data['end_date'] = $child_end_date;
    
                        $this->updateIssue($data);
    
                        if ($child_issue['child_issues']) {
                            foreach ($child_issue['child_issues'] as $grandchild_issue) {
                                $grandchild_start_date = Carbon::parse($grandchild_issue['start_date']);
                                $grandchild_end_date = Carbon::parse($grandchild_issue['end_date']);
                
                                if ($child_start_date->gt($grandchild_start_date) || $child_end_date->lt($grandchild_end_date)) {
                                    $diff = $child_start_date->diffInDays($grandchild_start_date);
                
                                    if ($child_start_date->lt($grandchild_start_date)) {
                                        $grandchild_start_date->subDays($diff);
                                        $grandchild_end_date->subDays($diff);
                                    } else {
                                        $grandchild_start_date->addDays($diff);
                                        $grandchild_end_date->addDays($diff);
                                    }

                                    if ($diff > 0 || $grandchild_end_date->isAfter($child_end_date)) {
                                        if ($grandchild_end_date->isAfter($child_end_date)) {
                                            $grandchild_end_date = $child_end_date;
                                        }

                                        $data = [];
                                        $data['issue_id'] = $grandchild_issue['issue_id'];
                                        $data['start_date'] = $grandchild_start_date;
                                        $data['end_date'] = $grandchild_end_date;
                    
                                        $this->updateIssue($data);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function updateParentIssueTimeline($data) {
        $start_date = Carbon::parse($data['start_date']);
        $end_date = Carbon::parse($data['end_date']);

        $data['issue_id'] = $data['parent_issue_id'];
        $parent_issue = $this->getIssueData($data);
        if ($parent_issue) {
            $parent_start_date = Carbon::parse($parent_issue['start_date']);
            $parent_end_date = Carbon::parse($parent_issue['end_date']);
            
            if ($start_date->lt($parent_start_date) || $end_date->gt($parent_end_date)) {
                if ($start_date->lt($parent_start_date)) {
                    $parent_start_date = $start_date;
                }
                if ($end_date->gt($parent_end_date)) {
                    $parent_end_date = $end_date;
                }

                $data = [];
                $data['issue_id'] = $parent_issue['issue_id'];
                $data['start_date'] = $parent_start_date;
                $data['end_date'] = $parent_end_date;
    
                $this->updateIssue($data);
            }

            if ($parent_issue['parent_issue_id']) {
                $data = [];
                $data['issue_id'] = $parent_issue['parent_issue_id'];
                $grandparent_issue = $this->getIssueData($data);

                $grandparent_start_date = Carbon::parse($grandparent_issue['start_date']);
                $grandparent_end_date = Carbon::parse($grandparent_issue['end_date']);

                if ($start_date->lt($grandparent_start_date) || $end_date->gt($grandparent_end_date)) {
                    if ($start_date->lt($grandparent_start_date)) {
                        $grandparent_start_date = $start_date;
                    }
                    if ($end_date->gt($grandparent_end_date)) {
                        $grandparent_end_date = $end_date;
                    }

                    $data = [];
                    $data['issue_id'] = $grandparent_issue['issue_id'];
                    $data['start_date'] = $grandparent_start_date;
                    $data['end_date'] = $grandparent_end_date;
        
                    $this->updateIssue($data);
                }
            }
        }
    }

    public function updateParentIssueStatus($data) {
        $data['issue_id'] = $data['parent_issue_id'];
        $parent_issue = $this->getIssueData($data);

        // hardcoded complete flg to 5
        if ($parent_issue['status_id'] == 5) {
            // hardcoded processing flg to 2
            $data = [];
            $data['issue_id'] = $parent_issue['issue_id'];
            $data['status_id'] = 2;
            $this->updateIssue($data);

            if ($parent_issue['parent_issue_id']) {
                $data = [];
                $data['issue_id'] = $parent_issue['parent_issue_id'];
                $grandparent_issue = $this->getIssueData($data);

                if ($grandparent_issue['status_id'] == 5) {
                    $data['issue_id'] = $parent_issue['parent_issue_id'];
                    $data['status_id'] = 2;
                    $this->updateIssue($data);
                }
            }
        }
    }

    public function willOverlapWithChildIssue($issue_id, $start_date, $end_date) {
        $data["issue_id"] = $issue_id;
        $data["with_child_issues"] = true;
        $issue = $this->getIssueData($data);
        $start_date = Carbon::parse($start_date);
        $end_date = Carbon::parse($end_date);

        $flag = false;
        if ($issue['child_issues']) {
            foreach ($issue['child_issues'] as $child_issue) {
                $child_start_date = Carbon::parse($child_issue['start_date']);
                $child_end_date = Carbon::parse($child_issue['end_date']);

                if ($start_date->gt($child_start_date) || $end_date->lt($child_end_date)) {
                    $flag = true;
                    break;
                }
            }
        }
        return $flag;
    }

    public function willOverlapWithParentIssue($start_date, $end_date, $parent_issue_id) {
        $start_date = Carbon::parse($start_date);
        $end_date = Carbon::parse($end_date);

        $flag = false;
        $data['issue_id'] = $parent_issue_id;
        $parent_issue = $this->getIssueData($data);
        if ($parent_issue) {
            $parent_start_date = Carbon::parse($parent_issue['start_date']);
            $parent_end_date = Carbon::parse($parent_issue['end_date']);

            if ($start_date->lt($parent_start_date) || $end_date->gt($parent_end_date)) {
                $flag = true;
            }
        }
        return $flag;
    }

    public function parentIsCompleted($parent_issue_id) {
        $data['issue_id'] = $parent_issue_id;
        $parent_issue = $this->getIssueData($data);

        // hardcoded complete flg to 5
        return $parent_issue['status_id'] == 5;
    }

    public function childrenNotCompleted($issue_id) {
        $data["issue_id"] = $issue_id;
        $data["with_child_issues"] = true;
        $issue = $this->getIssueData($data);

        $flag = false;
        if ($issue['child_issues']) {
            foreach ($issue['child_issues'] as $child_issue) {
                // hardcoded complete flg to 5
                if ($child_issue['status_id'] != 5) {
                    $flag = true;
                    break;
                }
            }
        }
        return $flag;
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

        if (isset($data["with_child_issues"])) {
            array_push($appendQuerys, function ($query) {
                return $this->repository->addWithChildIssues($query);
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
        $data['create_user_id'] = Auth::user()->user_id;
        $issue = $this->repository->createModel($data);
        foreach ($data["issue_categories"] as $issue_category) {
            $issueCategoryData = [
                "issue_id" => $issue["issue_id"],
                "category_id" => $issue_category
            ];
            $this->issueCategoryService->store($issueCategoryData);
        }
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

    public function updateIssue(array $data)
    {
        $this->updateModelById($data, $data["issue_id"]);
    }

    private function deleteByIssueId($issueId)
    {
        $this->issueCategoryService->deleteByIssueId($issueId);
    }

    public function deleteByProjectId($projectId)
    {
        $data['project'] = $projectId;
        $issues = $this->getIssues($data);
        foreach ($issues as $issue) {
            $this->issueCategoryService->deleteByIssueId($issue['issue_id']);
        }

        $this->repository->deleteByProjectId($projectId);
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
        $this->issueCategoryService->deleteByIssueId($issueId);

        $data['issue_id'] = $issueId;
        $data['with_child_issues'] = true;
        $issue = $this->getIssueData($data);

        if ($issue['child_issues']) {
            foreach ($issue['child_issues'] as $child_issue) {
                $this->repository->deleteModelById($child_issue['issue_id']);
                $this->issueCategoryService->deleteByIssueId($child_issue['issue_id']);

                if ($child_issue['child_issues']) {
                    foreach ($child_issue['child_issues'] as $grandchild_issue) {
                        $this->repository->deleteModelById($grandchild_issue['issue_id']);
                        $this->issueCategoryService->deleteByIssueId($grandchild_issue['issue_id']);
                    }
                }
            }
        }
    }
}
