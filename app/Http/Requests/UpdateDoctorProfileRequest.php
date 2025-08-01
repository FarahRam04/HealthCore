<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileRequest extends FormRequest
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
            'department_id' => 'nullable|exists:departments,id',
            'certificate'=>'nullable|string',
            'qualifications'=>'nullable|string',
            'years_of_experience'=>'nullable|numeric',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio'=>'required|string',
        ];
    }
}
