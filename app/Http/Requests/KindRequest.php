<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KindRequest extends FormRequest
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
            'kind_name' => [
                'required',
                'max:255',
                Rule::unique('kinds')->ignore($this->kind_id, 'kind_id')
            ],
            'kind_name' => ['required', 'max:255'],
            'hex_color' => ['required', 'max:7'],
        ];

        if ($this->isMethod('put')) {
            $editRules = [
                'kind_id' => ['required', 'integer'],
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
            'kind_name' => '種別名'
        ];

        return $rules;
    }
}
