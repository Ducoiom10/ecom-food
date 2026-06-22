<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:20',
            'options'    => 'nullable|array',
            'options.*'  => 'integer|exists:product_option_values,id',
            'note'       => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists'   => 'Sản phẩm không tồn tại.',
            'quantity.required'   => 'Vui lòng nhập số lượng.',
            'quantity.integer'    => 'Số lượng phải là số nguyên.',
            'quantity.min'        => 'Số lượng tối thiểu là 1.',
            'quantity.max'        => 'Số lượng tối đa là 20.',
            'options.*.exists'    => 'Biến thể không hợp lệ.',
        ];
    }
}

