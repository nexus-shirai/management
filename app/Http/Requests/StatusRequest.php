<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatusRequest extends FormRequest
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
            'status_name' => [
                'required',
                'max:255',
                Rule::unique('statuses')->ignore($this->status_id, 'status_id')
            ]
        ];

        if ($this->isMethod('put')) {
            $newRules = [
                'status_id' => ['required', 'integer'],
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
            'status_name' => '状態名'
        ];

        return $rules;
    }
}
