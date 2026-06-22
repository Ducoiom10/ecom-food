<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartQtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'nullable|integer|min:0|max:20',
            'options'   => 'nullable|array',
            'options.*' => 'integer|exists:product_option_values,id',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.integer'  => 'Số lượng phải là số nguyên.',
            'quantity.min'      => 'Số lượng tối thiểu là 0.',
            'quantity.max'      => 'Số lượng tối đa là 20.',
            'options.*.exists'    => 'Biến thể không hợp lệ.',
        ];
    }
}

