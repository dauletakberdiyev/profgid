<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'question',
        'talent_id',
    ];

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }
}
