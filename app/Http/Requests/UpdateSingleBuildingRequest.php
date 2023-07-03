<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSingleBuildingRequest extends FormRequest
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
            "building_code"  => [ // mã tòa nhà
                "required",
                Rule::unique('buildings', 'building_code')->where('is_deleted', 0)->ignore($this->id),
            ],
            "building_name" => [ // tên tòa nhà
                "required",
            ],
            "building_company" => [ // chủ sở hữu
                "",
            ],
            "building_address"  => [ // địa chỉ
                "required",
            ],
            "building_longitude"   => [ // kinh độ
                "required",
            ],
            "building_latitude" =>[ // vĩ độ
                "required",
            ],
            "contact_name"     => [ // đầu mối tòa nhà
                ""
            ],
            "contact_phone"     => [ // SDT đầu mối
                "",
                "digits_between:10,11",
                Rule::unique('buildings', 'building_code')->where('is_deleted', 0)->ignore($this->id),
            ],
            "partner_id"     => [ // mã đối tác
                "required"
            ],
            "cooperate_type"     => [ // Loại hình hợp tác
                "required",
                Rule::in(['1', '2', '3'])
            ],
            "percent_share"     => [ // Tỉ lệ chia sẻ
                "required",
                "numeric",
                "min:0",
                "max:100"
            ],
            "is_active"     => [// Trạng thái
                "required",
                Rule::in(['1', '2'])
            ]
        ];
    }

    public function messages()
    {
        return [
            'building_code.required'        => "Thiếu mã tòa nhà",
            'building_code.unique'          => "Mã tòa nhà đã tồn tại",
            'building_name.required'        => "Thiếu tên tòa nhà",
            'building_address.required'     => "Thiếu địa cho",
            'building_longitude.required'   => "Thiếu kinh độ",
            'building_latitude.required'    => "Thiếu vĩ độ",
            'contact_name.required'         => "Thiếu đầu mối tòa nhà",
            'contact_phone.required'        => "Thiếu số điện thoại liên hệ",
            'contact_phone.digits_between'  => "Số điện thoại liên hệ phải là số và có 10 hoặc 11 số",
            'partner_id.required'           => "Thiếu mã đối tác",
            "cooperate_type.required"       => "Thiếu loại hình hợp tác",
            "cooperate_type.in"             => "Loại hình hợp tác không hợp lệ",
            "percent_share.required"        => "Thiếu tỉ lệ chia sẻ",
            "percent_share.numeric"         => "Tỉ lệ chia sẻ phải là số, nhỏ nhất 0, lớn nhất 100",
            "percent_share.max"             => "Tỉ lệ chia sẻ phải là số, nhỏ nhất 0, lớn nhất 100",
            "percent_share.min"             => "Tỉ lệ chia sẻ phải là số, nhỏ nhất 0, lớn nhất 100",
            "is_active.required"            => "Thiếu trạng thái",
            "is_active.in"                  => "Trạng thái không hợp lệ",
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
