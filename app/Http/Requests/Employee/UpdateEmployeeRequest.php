<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
            $id = $this->route('employee')->id;
    
        return [
            'fullName'         => ['required', 'string', 'max:100'],
            'firstName'        => ['nullable', 'string', 'max:50'],
            'middleName'       => ['nullable', 'string', 'max:50'],
            'lastName'         => ['nullable', 'string', 'max:50'],
            'gender'           => ['nullable', 'in:Male,Female,Other'],
            'civilStatus'      => ['nullable', 'string', 'max:20'],
            'dateOfBirth'      => ['nullable', 'date'],
            'email' => ['nullable', 'email', "unique:users,email,{$id},id"],
            'phoneNumber'      => ['nullable', 'string', 'max:20'],
            'addressStreet'    => ['nullable', 'string', 'max:255'],
            'addressBarangay'  => ['nullable', 'string', 'max:100'],
            'addressCity'      => ['nullable', 'string', 'max:100'],
            'addressProvince'  => ['nullable', 'string', 'max:100'],
            'addressRegion'    => ['nullable', 'string', 'max:100'],
            'addressZipCode'   => ['nullable', 'string', 'max:10'],
            'department'       => ['nullable', 'string', 'max:100'],
            'position'         => ['nullable', 'string', 'max:100'],
            'branch'           => ['required', 'string', 'max:100'],
            'hireDate'         => ['nullable', 'date'],
            'basicSalary'      => ['required', 'numeric', 'min:0'],
            'employmentStatus' => ['required', 'in:probationary,regular,resigned,terminated'],
            'role'             => ['required', 'in:employee,hr,accounting,admin'],
            'username'         => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'template_id'      => ['nullable', 'exists:schedule_templates,id'],
            'effective_date'   => ['nullable', 'date'],
        ];
    }
}
