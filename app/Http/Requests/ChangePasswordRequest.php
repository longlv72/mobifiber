<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
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
            "old_password"    => [
                "required",
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        return $fail(__('Mật khẩu hiện tại không đúng.'));
                    }
                }
            ],
            "new_password" => [
                "required",
                'min:6'
            ],
        ];
    }

    public function messages()
    {
        return [
            "old_password.required"     => "Thiếu mật khẩu cũ" ,
            "new_password.required"     => "Thiếu mật khẩu mới" ,
            "new_password.min"          => "Mật khẩu tối thiểu 6 kí tự" ,
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first() . "."
            ], 200)
        );
    }
}
