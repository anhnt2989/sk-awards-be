<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Judge extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'name', 'email', 'specialty', 'judge_group', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_judges', 'judge_id', 'program_id')
            ->withPivot('added_at');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(JudgeAssignment::class, 'judge_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'judge_id');
    }
}
