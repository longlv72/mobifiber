<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateEquipmentInstallationJob extends FormRequest
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
            'job_id'   => [
                "required",
                Rule::exists('jobs', 'id')->where('is_deleted', 0)
            ],
            'contract_id'   => [
                "required",
                Rule::exists('contracts', 'id')->where('is_deleted', 0)
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
            'job_id.required'               => "Thiếu ID công việc",
            'job_id.exists'                 => "Công việc không tồn tại",
            'contract_id.required'          => "Thiếu hợp đồng",
            'contract_id.exists'            => "Thông tin hợp đồng không hợp lệ",
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
