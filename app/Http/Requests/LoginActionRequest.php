<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginActionRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username'  => 'required',
            'password'  => 'required|min:6'
        ];
    }

    public function messages() {
        return [
            'username.required' => "Thiếu tên đăng nhập",
            'password.required' => "Thiếu mật khẩu",
            'password.min'      => "Mật khẩu tối thiểu 6 kí tự"
        ];
    }
}
