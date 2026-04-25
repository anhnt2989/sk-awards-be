<?php

namespace App\Concerns;

class FileUploadValidationRules
{
    public static function uploadFileRules(): array
    {
        return [
            'files' => ['required', 'array', 'max:5'],
            'files.*' => [
                'required',
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
            ],
        ];
    }
}
