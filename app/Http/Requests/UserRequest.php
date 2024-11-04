<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0|max:100',
            'imageUrl' => 'nullable|image|mimes:jpg,jpeg,png,gif,JPG,PNG|max:2048',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->user->id ?? ''),
            'password' => 'nullable|string|min:8',
        ];
    }
}
