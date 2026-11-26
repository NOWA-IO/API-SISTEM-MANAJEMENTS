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
<<<<<<< HEAD
        $isPost = $this->isMethod('post');

        return [
            'name' => $isPost ? 'required|string|max:255' : 'sometimes|required|string|max:255',
            'category' => $isPost ? 'required|string|in:dry,wet' : 'sometimes|required|string|in:dry,wet',
            'price' => $isPost ? 'required|numeric|min:0' : 'sometimes|required|numeric|min:0',
            'weight' => $isPost ? 'required|numeric|min:0' : 'sometimes|required|numeric|min:0',
=======
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|in:dry,wet',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
        ];
    }

    /**
<<<<<<< HEAD
     * Custom message for validation errors.
=======
     * Custom validation messages.
     *
     * @return array<string, string>
>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama barang wajib diisi.',
            'name.string' => 'Nama barang harus berupa teks.',
            'name.max' => 'Nama barang maksimal 255 karakter.',

<<<<<<< HEAD
            'category.required' => 'Kategori barang wajib dipilih.',
            'category.string' => 'Kategori barang harus berupa teks.',
            'category.in' => 'Kategori barang tidak valid. Pilihan kategori yang tersedia: dry, wet.',

            'price.required' => 'Harga barang wajib diisi.',
            'price.numeric' => 'Harga barang harus berupa angka.',
            'price.min' => 'Harga barang minimal 0.',

            'weight.required' => 'Berat barang wajib diisi.',
            'weight.numeric' => 'Berat barang harus berupa angka.',
            'weight.min' => 'Berat barang minimal 0.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
=======
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

>>>>>>> 22570f13b564ab9f7273958c4f1ec2607bb21377
    public function failedValidation(Validator $validator)
    {
        return ResponseHelper::error($validator->errors(), trans('alert.validation_errors') ?? 'Validasi gagal.');
    }
}
