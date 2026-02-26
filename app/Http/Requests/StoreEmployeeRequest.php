<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            // columns that needs a rules
            'employee_id' => 'required|unique:employees,employee_id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'bureau' => 'sometimes|string|max:20',
            'bureau' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'salary' => 'sometimes|numeric|min:0',
            'employment_type' => 'required|in:Permanent,Contractual,Job Order',
            'role' => 'required|in:Admin,Employee',
            'is_active' => 'sometimes|boolean',
            'username' => 'sometimes|string|unique:employees,username',
            'password' => 'sometimes|string',
        ];
    }
}
