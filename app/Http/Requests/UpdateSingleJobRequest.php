<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSingleJobRequest extends FormRequest
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
            'address_id'   => [
                "nullable",
                "required_without:building_id",
                Rule::exists('customer_addresses', 'id')->where('is_deleted', 0)->where('customer_id', $this->customer_id)
            ],
            'building_id'  => [
                "nullable",
                'required_without:address_id',
                Rule::exists('buildings', 'id')->where('is_deleted', 0)->where('partner_id', $this->partner_id)
            ],
            'employee_ids'  => [
                'required',
            ],
            'job_status'  => [
                'required',
            ],
            'partner_id'   => [
                "required",
                Rule::exists('partners', 'id')->where('is_deleted', 0)
            ],
        ];
    }

    public function messages()
    {
        return [
            'building_id.required_without'  => "Thiếu địa chỉ hoặc tòa nhà",
            'building_id.exists'            => "Tòa nhà không tồn tại",
            'address_id.required_without'   => "Thiếu địa chỉ hoặc tòa nhà",
            'address_id.exists'             => "Địa chỉ khách hàng không tồn tại",
            'employee_ids.required'         => "Thiếu nhân viên thực hiện",
            'job_status.required'           => "Thiếu trạng thái công việc",
            'partner_id.required'           => "Thiếu đối tác",
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
