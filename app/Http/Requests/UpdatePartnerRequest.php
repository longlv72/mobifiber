<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePartnerRequest extends FormRequest
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
            "id"            => [
                "required"
            ],
            "partner_name"  => [
                "required",
            ],
            "partner_email" => [
                "required",
                Rule::unique('partners', 'email')->ignore($this->id)->where('is_deleted', 0),
                "email:rfc,dns"
            ],
            "partner_phone" => [
                "required",
                Rule::unique('partners', 'phone')->ignore($this->id)->where('is_deleted', 0),
            ],
            "partner_code" => [
                "required",
                Rule::unique('partners', 'partner_code')->ignore($this->id)->where('is_deleted', 0),
            ],
            "number_bank"   => [
                "required",
                Rule::unique('partners', 'number_bank')->ignore($this->id)->where('is_deleted', 0)
            ],
            "business_license" =>[
                "required",
                Rule::unique('partners', 'business_license')->ignore($this->id)->where('is_deleted', 0)
            ],
            "bank_name"     => [
                "required"
            ],
            "cooperate"     => [
                "required"
            ]
        ];
    }

    public function messages()
    {
        return [
            'id.required'                  => "Thiếu id đối tác",
            'partner_name.required'        => "Thiếu tên đối tác",
            'partner_email.unique'         => "Email đã tồn tại",
            'partner_email.required'       => "Thiếu thông tin email đối tác",
            'partner_email.email'          => "Email không đúng định dạng",
            'partner_phone.required'       => "Thiếu số điện thoại",
            'partner_phone.unique'         => "Số điện thoại đã tồn tại",
            "business_license.unique"       => "Giấy phép kinh doanh đã tồn tại",
            "business_license.required"       => "Thiếu giấy phép kinh doanh",
            'partner_code.required'        => "Thiếu mã code",
            'partner_code.unique'          => "Mã code đã tồn tại",
            "number_bank.required"         => "Thiếu số tài khoản ngân hàng",
            "number_bank.unique"           => "Số tài khoản ngân hàng đã tồn tại",
            "bank_name.required"           => "Thiếu tên ngân hàng",
            "cooperate.required"           => "Thiếu loại hình hợp tác",
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
