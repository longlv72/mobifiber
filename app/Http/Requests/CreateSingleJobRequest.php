<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSingleJobRequest extends FormRequest
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
            'job_code'  => [
                'required',
                Rule::unique('jobs', 'job_code')->where('is_deleted', 0)
            ],
            'job_type'  => [
                'required'
            ],
            'partner_id'   => [
                "nullable",
                "required_without:address_id",
                Rule::exists('partners', 'id')->where('is_deleted', 0)
            ],
            'customer_id'  => [
                'required',
                Rule::exists('customers', 'id')->where('is_deleted', 0)
            ],
            'contract_id'   => [
                "nullable",
                "required_if:job_type,2,3",
                Rule::exists('contracts', 'id')->where('is_deleted', 0)->where('customer_id', $this->customer_id)
            ],
            'address_id'   => [
                "nullable",
                "required_without:partner_id",
                Rule::exists('customer_addresses', 'id')->where('is_deleted', 0)->where('customer_id', $this->customer_id)
            ],
            'building_id'  => [
                "nullable",
                'required_with:partner_id',
                Rule::exists('buildings', 'id')->where('is_deleted', 0)->where('partner_id', $this->partner_id)
            ],
            'employee_ids'  => [
                'required',
            ],
            'reason'  => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'job_code.required'             => "Thiếu mã công việc",
            'job_code.unique'               => "Mã công việc đã tồn tại",
            'job_type.required'             => "Thiếu loại công việc",
            'contract_id.required_if'       => "Thiếu hợp đồng",
            'contract_id.exists'            => "Thông tin hợp đồng không hợp lệ",
            'partner_id.required_without'   => "Thiếu đối tác",
            'building_id.required_with'       => "Thiếu tòa nhà hoặc địa chỉ",
            'address_id.required_without'   => "Thiếu tòa nhà hoặc địa chỉ",
            'customer_id.required'          => "Thiếu khách hàng",
            'employee_ids.required'         => "Thiếu nhân viên thực hiện",
            'reason.required'               => "Thiếu lý do cho công việc",
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
