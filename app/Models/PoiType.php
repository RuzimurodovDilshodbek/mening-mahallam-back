<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoiType extends Model
{
    protected $fillable = ['name', 'icon'];

    public function pointsOfInterest(): HasMany
    {
        return $this->hasMany(PointOfInterest::class);
    }
}
