<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class TrackOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_number' => 'required|string',
            'phone'        => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'order_number.required' => 'Vui lòng nhập mã đơn hàng.',
            'order_number.string'   => 'Mã đơn hàng không hợp lệ.',
            'phone.required'        => 'Vui lòng nhập số điện thoại.',
            'phone.string'          => 'Số điện thoại không hợp lệ.',
        ];
    }
}

