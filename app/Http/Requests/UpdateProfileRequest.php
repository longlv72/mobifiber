<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
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
            "first_name"    => [
                "required",
            ],
            "last_name" => [
                "required",
            ],
            "email" => [
                "required",
                Rule::unique('users', 'email')->ignore(Auth::user()->id)->where('is_deleted', 0),
                "email:rfc,dns"
            ],
            "phone" => [
                "required",
                Rule::unique('users', 'email')->ignore(Auth::user()->id)->where('is_deleted', 0)
            ],
        ];
    }

    public function messages()
    {
        return [
            "first_name.required"       => "Thiếu tên" ,
            "last_name.required"        => "Thiếu họ đệm" ,
            "email.required"            => "Thiếu thông tin email" ,
            "email.unique"              => "Email đã tồn tại" ,
            "email.email"               => "Email không đúng định dạng",
            "phone.required"            => "Thiếu số điện thoại" ,
            "phone.unique"              => "Số điện thoại đã tồn tại" ,
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
