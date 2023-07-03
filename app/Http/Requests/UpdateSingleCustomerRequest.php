<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSingleCustomerRequest extends FormRequest
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
            "id"      => [
                "required",
                Rule::exists('customers', 'id')->where('is_deleted', 0)
            ],
            "code"    => [
                "required",
            ],
            "firstname"    => [
                "required",
            ],
            "username" => [
                "required",
                Rule::unique('customers', 'username')->ignore($this->id)->where('is_deleted', 0)
            ],
            "cccd"    => [
                "required",
                Rule::unique('customers', 'cccd')->ignore($this->id)->where('is_deleted', 0)
            ],
            "email" => [
                "required",
                Rule::unique('customers', 'email')->ignore($this->id)->where('is_deleted', 0),
                "email:rfc,dns"
            ],
            "phone" => [
                "required",
                Rule::unique('customers', 'phone')->ignore($this->id)->where('is_deleted', 0)
            ],
        ];
    }

    public function messages()
    {
        return [
            "id.required"               => "Thiếu id khách hàng",
            "id.exists"                 => "Thông tin khách hàng không hợp lệ",
            "first_name.required"       => "Thiếu họ và đệm",
            "email.required"            => "Thiếu thông tin email",
            "email.unique"              => "Email đã tồn tại",
            "email.email"               => "Email không đúng định dạng",
            "phone.required"            => "Thiếu số điện thoại",
            "phone.unique"              => "Số điện thoại đã tồn tại",
            "username.required"         => "Thiếu tài khoản khách hàng",
            "cccd.required"             => "Thiếu giấy tờ tùy thân",
            "cccd.unique"               => "Giấy tờ tùy thân đã tồn tại",
            "code.required"             => "Thiếu mã khách hàng",
            "username.unique"           => "Tài khoản khách hàng đã tồn tại"
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
