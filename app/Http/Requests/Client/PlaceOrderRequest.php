<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method'   => 'required|in:momo,bank,cod,zalopay',
            'delivery_mode'    => 'required|in:pickup,delivery',
            'branch_id'        => 'required|exists:branches,id',
            'delivery_address' => 'required_if:delivery_mode,delivery|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required'   => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in'         => 'Phương thức thanh toán không hợp lệ.',
            'delivery_mode.required'    => 'Vui lòng chọn hình thức nhận hàng.',
            'delivery_mode.in'          => 'Hình thức nhận hàng không hợp lệ.',
            'branch_id.required'        => 'Vui lòng chọn chi nhánh.',
            'branch_id.exists'          => 'Chi nhánh không tồn tại.',
            'delivery_address.required_if' => 'Vui lòng nhập địa chỉ giao hàng.',
            'delivery_address.string'   => 'Địa chỉ giao hàng không hợp lệ.',
        ];
    }
}

