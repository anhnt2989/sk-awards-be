<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Score extends Model
{
    protected $fillable = ['submission_id', 'judge_id', 'comment', 'is_submitted'];

    protected function casts(): array
    {
        return ['is_submitted' => 'boolean'];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(Judge::class, 'judge_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ScoreDetail::class, 'score_id');
    }

    public function total(): int
    {
        return $this->details->sum('value');
    }
}
