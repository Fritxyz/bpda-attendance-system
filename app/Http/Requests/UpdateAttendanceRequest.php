<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
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
            'am_in'   => 'nullable',
            'am_out'  => 'nullable',
            'pm_in'   => 'nullable',
            'pm_out'  => 'nullable',
            'ot_in'   => 'nullable',
            'ot_out'  => 'nullable',
            'remarks' => 'required|string|min:3', // Siguraduhin na nag-type ka rito sa form
        ];
    }
}
