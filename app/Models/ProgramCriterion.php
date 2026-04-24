<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramCriterion extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'program_id', 'name', 'description', 'max_score', 'sort_order'];

    protected function casts(): array
    {
        return ['max_score' => 'integer', 'sort_order' => 'integer'];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
