<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role'       => 'required|string|in:super_admin,branch_manager,coordinator,kitchen_staff,support',
            'permission' => 'required|string',
            'allowed'    => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'role.required'    => 'Vui lòng chọn vai trò.',
            'role.string'      => 'Vai trò không hợp lệ.',
            'role.in'          => 'Vai trò được chọn không hợp lệ.',
            'permission.required' => 'Vui lòng chọn quyền hạn.',
            'permission.string'   => 'Quyền hạn không hợp lệ.',
            'allowed.required' => 'Vui lòng chọn trạng thái cho phép.',
            'allowed.boolean'  => 'Trạng thái cho phép không hợp lệ.',
        ];
    }
}

