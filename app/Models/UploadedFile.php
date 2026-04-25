<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * Class UploadedFile
 *
 * @property integer id
 * @property string fileable_type
 * @property integer fileable_id
 * @property string user_id
 * @property string disk
 * @property string path
 * @property string original_name
 * @property string extension
 * @property string mime_type
 * @property integer size
 * @property string url
 * @property integer created_at
 * @property integer updated_at
 *
 * @package App\Models
 */
class UploadedFile extends Model
{
    protected $table = 'uploaded_files';

    protected $fillable = [
        'fileable_type',
        'fileable_id',
        'user_id',
        'disk',
        'path',
        'original_name',
        'extension',
        'mime_type',
        'size',
    ];

    protected $appends = ['url'];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): ?string
    {
        return $this->disk === 'public'
            ? asset(Storage::disk($this->disk)->url($this->path))
            : null;
    }
}
