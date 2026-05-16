<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempScoreDetail extends Model
{
    public $timestamps = false;

    protected $fillable = ['temp_score_id', 'criterion_id', 'value'];

    protected function casts(): array
    {
        return ['value' => 'integer'];
    }

    public function tempScore(): BelongsTo
    {
        return $this->belongsTo(TempScore::class, 'temp_score_id');
    }
}
