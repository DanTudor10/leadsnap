<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }
}
