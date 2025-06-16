<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Talent extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'talent_domain_id'
    ];

    protected $table = 'talents';


    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(TalentDomain::class, 'talent_domain_id');
    }

    public function professions(): BelongsToMany
    {
        return $this->belongsToMany(Profession::class)
                    ->withPivot('coefficient')
                    ->withTimestamps();
    }

    public function spheres(): BelongsToMany
    {
        return $this->belongsToMany(Sphere::class)
                    ->withPivot('coefficient')
                    ->withTimestamps();
    }
}
