<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Neighborhood extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'boundary_coordinates',
        'center_lat',
        'center_lng',
        'area',
        'region',
        'district',
        'city',
        'status',
        'is_verified',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'boundary_coordinates' => 'array',
        'is_verified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class);
    }

    public function pointsOfInterest(): HasMany
    {
        return $this->hasMany(PointOfInterest::class);
    }

    public function getGeojsonAttribute(): array
    {
        $coordinates = array_map(static fn(array $point): array => [$point[1], $point[0]], $this->boundary_coordinates ?? []);

        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
            ],
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => [$coordinates],
            ],
        ];
    }
}

