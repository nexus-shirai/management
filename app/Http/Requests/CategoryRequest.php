<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
            'category_name' => [
                'required',
                'max:255',
                Rule::unique('categories')->ignore($this->category_id, 'category_id')
            ]
        ];

        if ($this->isMethod('put')) {
            $newRules = [
                'category_id' => ['required', 'integer'],
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
            'category_name' => 'カテゴリー名'
        ];

        return $rules;
    }
}
