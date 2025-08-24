<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $question
 * @property int $talent_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Talent $talent
 */
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
