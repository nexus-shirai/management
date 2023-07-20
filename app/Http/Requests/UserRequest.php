<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserRequest extends FormRequest
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
            'user_type' => ['required', Rule::in([User::USER_TYPE_ADMIN, User::USER_TYPE_GENERAL])],
            'username' => ['required', 'max:255'],
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'first_name_kana' => ['required', 'max:255'],
            'last_name_kana' => ['required', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user_id, 'user_id'),
                'max:255'
            ],
        ];

        if ($this->isMethod('post')) {
            $storeRules = [
                'password' => ['required', 'min:8', 'max:20'],
                'confirm_password' => ['required_with:password', 'same:password'],
            ];
            $rules = array_merge($storeRules,  $rules);
        }

        if ($this->isMethod('put')) {
            $editRules = [
                'user_id' => ['required', 'integer'],
            ];

            if ($this->change_password) {
                $editRules['current_password'] = [];
                array_push($editRules['current_password'],
                    'required',
                    'min:8',
                    'max:20',
                    function ($attribute, $value, $fail) {
                        if (!Hash::check($value, Auth::user()->password)) {
                            return $fail(__('現在パスワードが間違っています。'));
                        }
                    }
                );
                $editRules['password'] = [];
                array_push($editRules['password'], 'required', 'min:8', 'max:20');
                $editRules['confirm_password'] = [];
                array_push($editRules['confirm_password'], 'required_with:password', 'same:password');
            }

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
        return [
            'user_type' => 'ユーザー種別',
            'username' => 'ユーザー名',
            'last_name' => '性',
            'first_name' => '名',
            'last_name_kana' => 'セイ',
            'first_name_kana' => 'メイ',
            'email' => 'メールアドレス',
            'current_password' => '現在パスワード',
            'password' => ($this->isMethod('put') ? '新' : '') . 'パスワード',
            'confirm_password' => ($this->isMethod('put') ? '新' : '') . 'パスワード（再確認）',
        ];
    }
}
