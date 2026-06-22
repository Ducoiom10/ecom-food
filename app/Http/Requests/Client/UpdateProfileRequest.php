<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name'  => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email,' . $userId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.string'   => 'Họ tên không hợp lệ.',
            'name.max'      => 'Họ tên không được vượt quá 100 ký tự.',
            'email.email'   => 'Địa chỉ email không hợp lệ.',
            'email.unique'  => 'Email đã được sử dụng bởi tài khoản khác.',
        ];
    }
}

