<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class OrderCreateRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }


    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Müşteri ID gereklidir.',
            'items.required' => 'Sipariş öğeleri gereklidir.',
            'items.*.product_id.exists' => 'Geçersiz ürün ID.',
            'items.*.product_id.required' => 'Ürün ID zorunludur.',
            'items.*.quantity.required' => 'Her öğe için miktar gereklidir.',
            'items.*.quantity.integer' => 'Miktar pozitif bir değer olmalıdır.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator|\Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator): void
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors(),
            'message' => 'Validation failed. Please fix the errors and try again.',
        ], 422));
    }
}
