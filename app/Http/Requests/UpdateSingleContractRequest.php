<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSingleContractRequest extends FormRequest
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
            "id"                        => "required",
            "contract_code"             => [
                "required",
                Rule::unique('contracts', 'code')->ignore($this->id)
            ],
            "package_id"                => "required",
            "start_date_package"             => "required|date_format:Y-m-d",
            "device_id"                 => "required",
            "bill_price"                => "required",
            "bill_price_vat"            => "required",
            "partner_id"                => "required",
            "building_id"               => "required_if:type_cooperate,2,3",
            "type_cooperate"            => [
                "required",
                Rule::in(['1', '2', '3'])
            ],
            "contract_status"           => [
                "required",
                Rule::in(['1', '2'])
            ],
            "customer_code"             => "required",
            "employee_id"               => [
                "required",
                Rule::exists('users', 'id')->where('is_deleted', 0)
            ],
        ];
    }

    public function messages() {
        return [
            "id.required"                        => "Thiếu mã id hợp đồng",
            "contract_code.required"             => "Thiếu mã hợp đồng",
            "contract_code.unique"               => "Mã hợp đồng đã tồn tại",
            "package_id.required"                => "Thiếu gói cước",
            "building_id.required"               => "Thiếu tòa nhà",
            "start_date_package.required"        => "Thiếu ngày bắt đầu gói cước",
            "start_date_package.date_format"     => "Ngày bắt đầu gói cước không hợp lệ",
            "device_id.required"                 => "Thiếu thiết bị",
            "customer_id.required"               => "Thiếu khách hàng",
            "bill_date.date_format"              => "Ngày tạo hóa đơn không hợp lệ",
            "bill_price.required"                => "Thiếu số tiền",
            "bill_price_vat.required"            => "Thiếu số tiền sau thuế",
            "develop_name.required"              => "Thiếu đơn vị phát triển",
            "partner_infrastructure.required"    => "Thiếu đối tác hạ tầng",
            "type_cooperate.required"            => "Thiếu loại hình hợp tác",
            "type_cooperate.in"                  => "Loại hình hợp tác không hợp lệ",
            "contract_status.required"           => "Thiếu trạng thái hợp đồng",
            "contract_status.in"                 => "Trạng thái hợp đồng không hợp lệ",
            "customer_code.required"             => "Thiếu mã ID khách hàng",
            "employee_id.required"               => "Thiếu nhân viên",
            "employee_id.exists"                 => "Nhân viên không tồn tại",
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
