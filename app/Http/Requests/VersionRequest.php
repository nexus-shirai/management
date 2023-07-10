<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VersionRequest extends FormRequest
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
            'version_name' => [
                'required',
                'max:255',
                Rule::unique('versions')->ignore($this->version_id, 'version_id')
            ]
        ];

        if ($this->isMethod('put')) {
            $editRules = [
                'version_id' => ['required', 'integer'],
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
            'version_name' => 'バージョン名'
        ];

        return $rules;
    }
}
