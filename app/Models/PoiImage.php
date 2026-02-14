<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoiImage extends Model
{
    protected $fillable = ['point_of_interest_id', 'path', 'is_primary', 'sort_order'];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function pointOfInterest(): BelongsTo
    {
        return $this->belongsTo(PointOfInterest::class);
    }
}
