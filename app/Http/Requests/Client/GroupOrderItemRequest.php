<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class GroupOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'action'     => 'required|in:add,remove',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists'   => 'Sản phẩm không tồn tại.',
            'action.required'     => 'Vui lòng chọn hành động.',
            'action.in'           => 'Hành động không hợp lệ.',
        ];
    }
}

