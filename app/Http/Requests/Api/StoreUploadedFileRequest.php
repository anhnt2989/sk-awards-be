<?php

namespace App\Http\Requests\Api;

use App\Concerns\FileUploadValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreUploadedFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return FileUploadValidationRules::uploadFileRules();
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Vui lòng chọn ít nhất một file.',
            'files.array' => 'Danh sách file không hợp lệ.',
            'files.max' => 'Chỉ được upload tối đa 10 file.',
            'files.*.required' => 'Vui lòng chọn file.',
            'files.*.file' => 'Dữ liệu tải lên không hợp lệ.',
            'files.*.max' => 'Mỗi file không được vượt quá 5MB.',
            'files.*.mimes' => 'Định dạng file không được hỗ trợ.',
        ];
    }
}
