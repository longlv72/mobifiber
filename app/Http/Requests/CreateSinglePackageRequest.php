<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSinglePackageRequest extends FormRequest
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
            "package_name"    => [
                "required",
            ],
            "package_code" => [
                "required",
                Rule::unique('packages', 'package_code')->where('is_deleted', 0)
            ],
            "time_used" => [
                "required"
            ],
            "descision"     => "",
            "active_package" => "required",
        ];
    }

    public function messages()
    {
        return [
            "package_name.required"         => "Thiếu tên gói cước" ,
            "package_code.required"         => "Thiếu mã gói cước" ,
            "package_code.unique"           => "Mã gói cước đã tồn tại",
            "time_used.required"            => "Thiếu thời gian sử dụng" ,
            "active_package.required"       => "Thiếu trạng thái kích hoạt" ,
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
