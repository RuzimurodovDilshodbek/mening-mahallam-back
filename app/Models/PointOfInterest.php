<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PointOfInterest extends Model
{
    protected $table = 'points_of_interest';

    protected $fillable = [
        'neighborhood_id',
        'name',
        'type',
        'poi_type_id',
        'latitude',
        'longitude',
        'description',
        'address',
        'phone',
        'working_hours',
    ];

    protected $casts = [
        'working_hours' => 'array',
    ];

    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function poiType(): BelongsTo
    {
        return $this->belongsTo(PoiType::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PoiImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(PoiImage::class)->where('is_primary', true);
    }
}

