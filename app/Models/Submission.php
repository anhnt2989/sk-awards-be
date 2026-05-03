<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Submission extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id', 'program_id', 'name', 'company', 'submitter_id',
        'category_id', 'description', 'docs', 'submitted_date', 'status',
    ];

    protected function casts(): array
    {
        return ['submitted_date' => 'date'];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProgramCategory::class, 'category_id', 'id')
            ->where('program_id', $this->program_id);
    }

    public function assignedJudges(): BelongsToMany
    {
        return $this->belongsToMany(Judge::class, 'judge_assignments', 'submission_id', 'judge_id')
            ->withPivot('assigned_at');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'submission_id');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(UploadedFile::class, 'fileable');
    }
}
