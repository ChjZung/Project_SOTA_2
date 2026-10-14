<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InquiryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép tất cả người dùng gửi form (không cần đăng nhập)
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'    => $this->input('name') ?? $this->input('ten'),
            'phone'   => $this->input('phone') ?? $this->input('dienthoai'),
            'message' => $this->input('message') ?? $this->input('noidung') ?? $this->input('tieude'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['required', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:5'],
        ];
    }

    /**
     * Thông báo lỗi Tiếng Việt tùy chỉnh
     */
    public function messages(): array
    {
        return [
            'name.required'    => 'Vui lòng nhập họ tên.',
            'email.required'   => 'Vui lòng nhập địa chỉ email.',
            'email.email'      => 'Email không đúng định dạng.',
            'phone.required'   => 'Vui lòng nhập số điện thoại.',
            'message.required' => 'Vui lòng nhập nội dung yêu cầu.',
            'message.min'      => 'Nội dung yêu cầu phải có ít nhất 10 ký tự.',
        ];
    }
}