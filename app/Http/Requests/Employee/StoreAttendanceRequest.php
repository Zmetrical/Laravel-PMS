<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id'  => ['required', 'exists:users,id'],
            'date'     => ['required', 'date'],
            'time_in'  => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i'],
            'status'   => ['nullable', 'in:present,absent,late,half_day,leave,holiday,rest_day,incomplete'],
            'notes'    => ['nullable', 'string', 'max:500'],
        ];
    }
}