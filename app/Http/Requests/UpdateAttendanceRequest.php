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
            // Morning Session
            'am_in'  => ['nullable', 'date_format:H:i'],
            'am_out' => ['nullable', 'date_format:H:i', 'after:am_in'],

            // Afternoon Session
            // Kung may PM In, dapat after ito ng AM Out (kung may AM Out)
            'pm_in'  => ['nullable', 'date_format:H:i', 'after:am_out'], 
            
            // Eto ang sagot sa tanong mo: 
            // Ang PM Out ay dapat after ng PM In (kung meron), 
            // O after ng AM In (kung PM In ay blanko).
            'pm_out' => [
                'nullable', 
                'date_format:H:i', 
                function ($attribute, $value, $fail) {
                    if ($value) {
                        // Kunin ang pinaka-latest na entry bago ang PM Out
                        $startTime = $this->pm_in ?: $this->am_out ?: $this->am_in;
                        
                        if ($startTime && $value <= $startTime) {
                            $fail('The PM Out time must be later than the previous logs.');
                        }
                    }
                },
            ],

            // Overtime
            'ot_in'  => ['nullable', 'date_format:H:i', 'after:pm_out'],
            'ot_out' => ['nullable', 'date_format:H:i', 'after:ot_in'],

            'remarks' => ['required', 'string', 'min:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'am_out.after' => 'Morning out must be after morning in.',
            'pm_in.after'  => 'Afternoon in should be after morning out.',
            'pm_out.after' => 'Afternoon out must be after your last check-in.',
            'ot_out.after' => 'OT out must be after OT in.',
            'remarks.required' => 'Please provide a reason for this manual adjustment.',
        ];
    }
}
