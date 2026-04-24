<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id', 'name', 'year', 'abbr', 'color', 'status',
        'deadline', 'description', 'next_sub_id',
    ];

    protected function casts(): array
    {
        return [
            'year'        => 'integer',
            'next_sub_id' => 'integer',
            'deadline'    => 'date',
        ];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ProgramCategory::class, 'program_id')->orderBy('sort_order');
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(ProgramCriterion::class, 'program_id')->orderBy('sort_order');
    }

    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(Judge::class, 'program_judges', 'program_id', 'judge_id')
            ->withPivot('added_at');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'program_id');
    }

    /** Generate next sequential submission ID and increment counter. */
    public function generateSubmissionId(): string
    {
        $num    = str_pad($this->next_sub_id, 3, '0', STR_PAD_LEFT);
        $year   = substr((string) $this->year, 2);
        $id     = "{$this->abbr}{$year}-{$num}";

        $this->increment('next_sub_id');

        return $id;
    }
}
