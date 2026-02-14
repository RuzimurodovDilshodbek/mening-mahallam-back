<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Camera extends Model
{
    protected $fillable = [
        'neighborhood_id',
        'name',
        'type',
        'latitude',
        'longitude',
        'description',
        'model',
        'status',
    ];

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }
}

