<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            //
            // Dapat nage-exist ang ID sa employees table
            'employee_id' => 'required|exists:employees,employee_id',
            
            // Dapat isa lang ito sa mga valid choices mo sa form
            'attendance_mode' => 'required|in:AM IN,AM OUT,PM IN,PM OUT,OT IN,OT OUT',
        ];
    }
}
