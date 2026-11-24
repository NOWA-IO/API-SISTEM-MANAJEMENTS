<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'category' => 'required|in:dry,wet',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
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
            'name.required' => 'Nama barang wajib diisi.',
            'name.string' => 'Nama barang harus berupa teks.',
            'name.max' => 'Nama barang maksimal 255 karakter.',

            'category.required' => 'Kategori barang wajib diisi.',
            'category.in' => 'Kategori barang harus dry atau wet.',

            'price.required' => 'Harga barang wajib diisi.',
            'price.numeric' => 'Harga barang harus berupa angka.',
            'price.min' => 'Harga barang tidak boleh kurang dari 0.',

            'weight.required' => 'Berat barang wajib diisi.',
            'weight.numeric' => 'Berat barang harus berupa angka.',
            'weight.min' => 'Berat barang tidak boleh kurang dari 0.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return ResponseHelper::error($validator->errors(), trans('alert.validation_errors') ?? 'Validasi gagal.');
    }
}
