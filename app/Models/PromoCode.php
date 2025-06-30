<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'is_used',
        'used_by',
        'used_at',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'is_active' => 'boolean',
        'used_at' => 'datetime',
    ];

    /**
     * Get the user who used this promo code
     */
    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * Scope to get only active promo codes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only unused promo codes
     */
    public function scopeUnused($query)
    {
        return $query->where('is_used', false);
    }

    /**
     * Check if promo code is available for use
     */
    public function isAvailable()
    {
        return $this->is_active && !$this->is_used;
    }

    /**
     * Mark promo code as used
     */
    public function markAsUsed($userId)
    {
        $this->update([
            'is_used' => true,
            'used_by' => $userId,
            'used_at' => now(),
        ]);
    }

    /**
     * Generate a random 6-digit promo code
     */
    public static function generateCode()
    {
        do {
            $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
