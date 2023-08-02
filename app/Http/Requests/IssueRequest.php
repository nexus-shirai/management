<?php

namespace App\Http\Requests;

use App\Models\Issue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IssueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'project_id' => ['required', 'integer'],

            'kind_id' => ['required', 'integer'],
            'issue_title' => ['required', 'max:255'],
            'issue_desc' => ['required', 'max:255'],
            'status_id' => ['required', 'integer'],
            'assignee_id' => ['required', 'integer'],
            'issue_priority' => [
                'required',
                Rule::in([
                    Issue::PRIORITY_LOW,
                    Issue::PRIORITY_MEDIUM,
                    Issue::PRIORITY_HIGH
                ])
            ],
            'milestone_id' => ['required', 'integer'],
            'estimated_time' => ['nullable', 'integer'],
            'completion_time' => ['nullable', 'integer'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'complete_reason' => [
                'nullable',
                Rule::in([
                    Issue::COMPLETE_REASON_1,
                    Issue::COMPLETE_REASON_2,
                    Issue::COMPLETE_REASON_3,
                    Issue::COMPLETE_REASON_4,
                    Issue::COMPLETE_REASON_5,
                ])
            ],
            'version_id' => ['required', 'integer'],
            'issue_rank' => [
                'required',
                Rule::in([
                    Issue::RANK_PARENT,
                    Issue::RANK_CHILD,
                    Issue::RANK_GRANDCHILD,
                ])
            ],
            'parent_issue_id' => ['nullable', 'required_unless:issue_rank,' . Issue::RANK_PARENT, 'integer'],
            'issue_categories' => ['array', 'required']
        ];

        if ($this->isMethod('put')) {
            $editRules = [
                'issue_id' => ['required', 'integer'],
            ];
            $rules = array_merge($editRules,  $rules);
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        $rules = [
            'kind_id' => '種別',
            'issue_title' => '課題名',
            'issue_desc' => '課題の詳細',
            'status_id' => '状態',
            'assignee_id' => '担当者',
            'issue_priority' => '優先度',
            'milestone_id' => 'マイルストーン',
            'estimated_time' => '予定時間',
            'completion_time' => '実施時間',
            'start_date' => '開始日',
            'end_date' => '期限日',
            'version_id' => 'バージョン',
            'issue_categories' => 'カテゴリー',
            'issue_rank' => '課題ランク',
            'parent_issue_id' => '親課題',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'parent_issue_id.required_unless' => nl2br('課題ランクが親課題でない場合、<br/>親課題を指定してください。'),
        ];
    }
}
