<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        return [
            'user_type' => ['required', Rule::in([User::USER_TYPE_ADMIN, User::USER_TYPE_GENERAL])],
            'username' => ['required', 'max:255'],
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'first_name_kana' => ['required', 'max:255'],
            'last_name_kana' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users', 'max:255'],
            'password' => ['required', 'min:8', 'max:20'],
            'confirm_password' => ['required_with:password', 'same:password'],
        ];
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
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'confirm_password' => 'パスワード（再確認）',
        ];
    }
}
