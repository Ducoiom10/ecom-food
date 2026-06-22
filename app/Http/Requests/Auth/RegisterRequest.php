<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|max:15|unique:users,phone',
            'email'    => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Vui lòng nhập họ tên.',
            'name.string'        => 'Họ tên không hợp lệ.',
            'name.max'           => 'Họ tên không được vượt quá 100 ký tự.',
            'phone.required'     => 'Vui lòng nhập số điện thoại.',
            'phone.string'       => 'Số điện thoại không hợp lệ.',
            'phone.max'          => 'Số điện thoại không được vượt quá 15 ký tự.',
            'phone.unique'       => 'Số điện thoại đã được đăng ký.',
            'email.email'        => 'Địa chỉ email không hợp lệ.',
            'email.unique'       => 'Email đã được đăng ký.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.string'    => 'Mật khẩu không hợp lệ.',
            'password.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}

