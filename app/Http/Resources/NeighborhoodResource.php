<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NeighborhoodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'boundary_coordinates' => $this->boundary_coordinates,
            'center' => [
                'lat' => (float) $this->center_lat,
                'lng' => (float) $this->center_lng,
            ],
            'area' => (float) $this->area,
            'location' => [
                'region' => $this->region,
                'district' => $this->district,
                'city' => $this->city,
            ],
            'status' => $this->status,
            'is_verified' => (bool) $this->is_verified,
            'geojson' => $this->geojson,
            'user' => $this->whenLoaded('user', function (): array {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'cameras_count' => $this->whenCounted('cameras'),
            'pois_count' => $this->whenCounted('pointsOfInterest'),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

