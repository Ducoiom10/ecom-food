<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendPushRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'   => 'required|string',
            'body'    => 'required|string',
            'segment' => 'required|in:all,abandoned_cart,inactive_7d,vip',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'Vui lòng nhập tiêu đề thông báo.',
            'title.string'     => 'Tiêu đề không hợp lệ.',
            'body.required'    => 'Vui lòng nhập nội dung thông báo.',
            'body.string'      => 'Nội dung không hợp lệ.',
            'segment.required' => 'Vui lòng chọn nhóm đối tượng.',
            'segment.in'       => 'Nhóm đối tượng không hợp lệ.',
        ];
    }
}

