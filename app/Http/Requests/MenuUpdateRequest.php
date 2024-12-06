<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MenuUpdateRequest extends FormRequest
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
            'nama' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string|max:100',
            'ketersediaan' => 'nullable|integer',
            'harga' => 'nullable|integer',
            'gambar' => 'nullable|image|mimes:apng,avif,gif,jpeg,png,svg+xml,webp|max:2048'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Ada data yang tidak valid',
            'ref_code' => 'VALIDATION_FAILED',
            'detail' => $validator->errors(),
        ])->setStatusCode(422));
    }
}
