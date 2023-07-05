<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MilestoneRequest extends FormRequest
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
            'milestone_name' => [
                'required',
                'max:255',
                Rule::unique('milestones')->ignore($this->milestone_id, 'milestone_id')
            ]
        ];

        if ($this->isMethod('put')) {
            $newRules = [
                'milestone_id' => ['required', 'integer'],
            ];
            $rules = array_merge($newRules,  $rules);
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
            'milestone_name' => 'マイルストーン名'
        ];

        return $rules;
    }
}
