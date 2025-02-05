<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class CustomerCreateRequest extends FormRequest
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
            'name' => 'required',
            'since' => 'required|date_format:Y-m-d',
            'revenue' => 'required',
        ];
    }

    /**
     * Customize the error messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name alanı zorunludur.',
            'since.required' => 'Since alanı zorunludur.',
            'since.date_format' => 'Since alanı tarih formatında olmalıdır.',
            'revenue.required' => 'Revenue alanı zorunludur.',
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
