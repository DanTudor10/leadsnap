<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name', 
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}