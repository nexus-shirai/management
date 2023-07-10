<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
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
            'project_cd' => [
                'required',
                'max:12',
                Rule::unique('projects')->ignore($this->project_id, 'project_id'),
                'regex:/^[0-9A-Z_]+$/'
            ],
            'project_name' => ['required', 'max:255'],
            'project_desc' => ['required', 'max:255'],
            'project_users' => ['array', 'required'],
        ];

        if ($this->isMethod('put')) {
            $editRules = [
                'project_id' => ['required', 'integer'],
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
            'project_cd' => 'プロジェクトコード',
            'project_name' => 'プロジェクト名',
            'project_desc' => 'プロジェクト概要',
            'project_users' => '参加ユーザー',
        ];

        return $rules;
    }
}
