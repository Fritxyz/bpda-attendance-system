<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTravelOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Palitan ng true para payagan ang request
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
            'employee_id' => 'required|exists:employees,employee_id', // Siguraduhing existing ang employee
            'to_number'   => 'required|string',
            'destination' => 'required|string|max:255',
            'purpose'     => 'required|string',
            'date_from'   => 'required|date',
            'date_to'     => 'required|date|after_or_equal:date_from', // Bawal mauna ang Return date sa Departure
        ];
    }

    /**
     * Custom error messages (Optional)
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Please select an employee',
            'to_number.unique'     => 'TO is already used.',
            'date_to.after_or_equal' => 'Date To must be after or equal to the Date From',
        ];
    }
}
