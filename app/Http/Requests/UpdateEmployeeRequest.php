<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
        // Kunin ang ID mula sa route parameter
        $employeeId = $this->route('employee');

        return [
            // 'unique:table,column,except_id,id_column'
            'employee_id' => 'required|unique:employees,employee_id,' . $employeeId . ',employee_id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'sometimes|string|max:10|nullable',
            'bureau' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'salary' => 'numeric|min:0|nullable',
            'leave_credits' => 'numeric|min:0|nullable',
            'employment_type' => 'required|in:Permanent,Contractual',
            'role' => 'required|in:Admin,Employee',
            'is_active' => 'sometimes|boolean',
            'password' => 'sometimes|string|nullable',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2040',
        ];
    }
}
