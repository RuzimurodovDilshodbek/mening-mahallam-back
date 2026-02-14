<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NeighborhoodStoreRequest;
use App\Http\Requests\NeighborhoodUpdateRequest;
use App\Http\Resources\NeighborhoodResource;
use App\Models\Neighborhood;
use App\Services\GeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NeighborhoodController extends Controller
{
    public function __construct(private readonly GeoService $geoService)
    {
    }

    public function index(Request $request)
    {
        $query = Neighborhood::query()->with('user')->withCount(['cameras', 'pointsOfInterest'])->latest();

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return NeighborhoodResource::collection($query->paginate(10));
    }

    public function store(NeighborhoodStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $coords = $validated['boundary_coordinates'] ?? null;
        $center = $coords ? $this->geoService->calculateCenter($coords) : null;
        $area = $coords ? $this->geoService->calculateArea($coords) : null;

        $slugBase = Str::slug($validated['name']);
        $slug = $slugBase;
        $counter = 1;

        while (Neighborhood::query()->where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.$counter;
            $counter++;
        }

        $neighborhood = Neighborhood::query()->create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'boundary_coordinates' => $coords,
            'center_lat' => $center['lat'] ?? null,
            'center_lng' => $center['lng'] ?? null,
            'area' => $area,
            'region' => $validated['region'] ?? null,
            'district' => $validated['district'] ?? null,
            'city' => $validated['city'] ?? null,
            'status' => 'draft',
        ]);

        return (new NeighborhoodResource($neighborhood->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Neighborhood $neighborhood): NeighborhoodResource
    {
        if (! $request->user()->isAdmin() && $neighborhood->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        return new NeighborhoodResource($neighborhood->load('user'));
    }

    public function update(NeighborhoodUpdateRequest $request, Neighborhood $neighborhood): NeighborhoodResource
    {
        if (! $request->user()->isAdmin() && $neighborhood->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $validated = $request->validated();

        if (isset($validated['boundary_coordinates'])) {
            $center = $this->geoService->calculateCenter($validated['boundary_coordinates']);
            $validated['center_lat'] = $center['lat'];
            $validated['center_lng'] = $center['lng'];
            $validated['area'] = $this->geoService->calculateArea($validated['boundary_coordinates']);
        }

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']).'-'.$neighborhood->id;
        }

        $neighborhood->update($validated);

        return new NeighborhoodResource($neighborhood->load('user'));
    }

    public function destroy(Request $request, Neighborhood $neighborhood): JsonResponse
    {
        if (! $request->user()->isAdmin() && $neighborhood->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $neighborhood->delete();

        return response()->json(['message' => 'Neighborhood deleted']);
    }

    public function publish(Request $request, Neighborhood $neighborhood): NeighborhoodResource
    {
        if (! $request->user()->isAdmin() && $neighborhood->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        $neighborhood->update(['status' => 'published']);

        return new NeighborhoodResource($neighborhood->load('user'));
    }

    public function geojson(Request $request, Neighborhood $neighborhood): JsonResponse
    {
        if (! $request->user()->isAdmin() && $neighborhood->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }

        return response()->json($neighborhood->geojson);
    }
}

