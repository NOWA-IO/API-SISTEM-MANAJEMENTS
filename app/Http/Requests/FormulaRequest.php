<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class FormulaRequest extends FormRequest
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
        return [
            'product_id' => 'required|uuid|exists:products,id',
            'description' => 'required|string',
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
            'product_id.required' => 'Barang wajib dipilih.',
            'product_id.uuid' => 'Format ID barang tidak valid.',
            'product_id.exists' => 'Barang yang dipilih tidak ditemukan.',

            'description.required' => 'Deskripsi formula wajib diisi.',
            'description.string' => 'Deskripsi formula harus berupa teks.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ResponseHelper::error($validator->errors(), trans('alert.validation_errors') ?? 'Validasi gagal.');
    }
}
