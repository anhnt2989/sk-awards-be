<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreDetail extends Model
{
    public $timestamps = false;

    protected $fillable = ['score_id', 'criterion_id', 'value'];

    protected function casts(): array
    {
        return ['value' => 'integer'];
    }

    public function score(): BelongsTo
    {
        return $this->belongsTo(Score::class, 'score_id');
    }
}
