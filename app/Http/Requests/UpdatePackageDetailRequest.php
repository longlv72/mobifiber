<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePackageDetailRequest extends FormRequest
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
            "id"    => [
                "required",
                "numeric",
                Rule::exists('package_detail', 'id')->where('is_deleted', 0)
            ],
            "package_id"    => [
                "required",
                "numeric"
            ],
            "start_date"  => [
                "required",
                "date_format:Y-m-d"
            ],
            "end_date" => [
                "required",
                "date_format:Y-m-d",
                "after:start_date"
            ],
            "price" => [
                "required",
                "numeric",
            ],
            "price_vat"  => [
                "required",
                "numeric",
            ],
            "decision_number"   => [
                "required",
            ],
        ];
    }

    public function messages()
    {
        return [
            'id.required'                => "Thiếu mã chi tiết gói cước",
            'id.numeric'                 => "Mã mã chi tiết gói cước phải là số",
            'id.exists'                  => "Chi tiết gói cước không tồn tại",
            'package_id.required'        => "Thiếu mã gói cước",
            'package_id.numeric'         => "Mã gói cước phải là số",
            'start_date.required'        => "Thiếu ngày bắt đầu gói cước",
            'end_date.required'          => "Thiếu ngày kết thúc gói cước",
            'end_date.after'             => "Ngày kết thúc gói cước phải lớn hơn ngày bắt đầu gói cước",
            'price.required'             => "Thiếu giá gói cước",
            'price.numeric'              => "Giá gói cước phải là số",
            'price_vat.required'         => "Thiếu giá sau thuế",
            'price_vat.numeric'          => "Giá sau thuế phải là số",
            'decision_number.required'   => "Thiếu số quyết định gói cước",
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
