<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSingleEmployeeRequest extends FormRequest
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
            "username"  => [
                "required",
                Rule::unique('users', 'username')->where('is_deleted', 0)
            ],
            "email" => [
                "required",
                Rule::unique('users', 'email')->where('is_deleted', 0),
                "email::rfc,dns",
            ],
            "phone" => [
                "required",
                Rule::unique('users', 'phone')->where('is_deleted', 0)

            ],
            "password"      => "required|min:6",
            "is_active"     => "required",
            "role_group_id" => "required",
            "real_manage_unit" => [
                "required",
                Rule::in(['1', '2', '3'])
            ],
        ];
    }

    public function messages()
    {
        return [
            "first_name.required"       => "Thiếu tên đầu" ,
            "last_name.required"        => "Thiếu họ nhân viên" ,
            "username.required"         => "Thiếu username",
            "username.unique"           => "Username đã tồn tại",
            "email.required"            => "Thiếu thông tin email" ,
            "email.unique"              => "Email đã tồn tại" ,
            "email.email"               => "Email không đúng định dạng",
            "phone.required"            => "Thiếu số điện thoại",
            "phone.unique"              => "Số điện thoại đã tồn tại" ,
            "password.required"         => "Thiếu mật khẩu" ,
            "is_active.required"        => "Thiếu trạng thái kích hoạt" ,
            "role_group_id.required"    => "Thiếu loại tài khoản" ,
            "real_manage_unit.required" => "Thiếu đơn vị thực quản lý" ,
            "real_manage_unit.in"       => "Đơn vị thực quản lý không hợp lệ" ,
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 200)
        );
    }
}
