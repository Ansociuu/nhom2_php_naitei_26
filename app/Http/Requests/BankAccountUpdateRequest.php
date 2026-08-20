<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankAccountUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bank_name' => [
                'required',
                'string',
                Rule::in(array_keys(config('banks.list'))),
            ],
            'account_number' => [
                'required',
                'string',
                'min:6',
                'max:25',
                'regex:/^[0-9]+$/',
            ],
            'account_holder_name' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }

    /**
     * Custom error messages in Vietnamese.
     */
    public function messages(): array
    {
        return [
            'bank_name.required'            => 'Please select a bank.',
            'bank_name.in'                  => 'Invalid bank selected.',
            'account_number.required'       => 'Please enter the account number.',
            'account_number.regex'          => 'Account number can only contain digits.',
            'account_number.min'            => 'Account number must be at least 6 digits.',
            'account_number.max'            => 'Account number cannot exceed 25 digits.',
            'account_holder_name.required'  => 'Please enter the account holder name.',
            'account_holder_name.max'       => 'Account holder name cannot exceed 100 characters.',
        ];
    }
}
