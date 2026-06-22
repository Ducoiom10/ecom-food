<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignShipperRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id'   => 'required|exists:orders,id',
            'shipper_id' => 'required|exists:shippers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Vui lòng chọn đơn hàng.',
            'order_id.exists'   => 'Đơn hàng không tồn tại.',
            'shipper_id.required' => 'Vui lòng chọn tài xế.',
            'shipper_id.exists'   => 'Tài xế không tồn tại.',
        ];
    }
}

