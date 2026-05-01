<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.string'   => 'Mật khẩu hiện tại không hợp lệ.',
            'password.required'         => 'Vui lòng nhập mật khẩu mới.',
            'password.string'           => 'Mật khẩu mới không hợp lệ.',
            'password.min'              => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed'        => 'Xác nhận mật khẩu mới không khớp.',
        ];
    }
}

