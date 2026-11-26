<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeEarningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isPost = $this->isMethod('post');

        return [
            'user_id' => $isPost ? 'required|uuid|exists:users,id' : 'sometimes|required|uuid|exists:users,id',
            'income' => $isPost ? 'required|numeric|min:0' : 'sometimes|required|numeric|min:0',
            'total_income' => $isPost ? 'required|numeric|min:0' : 'sometimes|required|numeric|min:0',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Karyawan wajib dipilih.',
            'user_id.uuid' => 'Format ID karyawan tidak valid.',
            'user_id.exists' => 'Karyawan yang dipilih tidak ditemukan.',

            'income.required' => 'Penghasilan wajib diisi.',
            'income.numeric' => 'Penghasilan harus berupa angka.',
            'income.min' => 'Penghasilan minimal 0.',

            'total_income.required' => 'Total penghasilan wajib diisi.',
            'total_income.numeric' => 'Total penghasilan harus berupa angka.',
            'total_income.min' => 'Total penghasilan minimal 0.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ResponseHelper::error($validator->errors(), trans('alert.validation_errors') ?? 'Validasi gagal.');
    }
}
