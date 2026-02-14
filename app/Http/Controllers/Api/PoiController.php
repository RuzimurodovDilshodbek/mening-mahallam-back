<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood;
use App\Models\PoiImage;
use App\Models\PoiType;
use App\Models\PointOfInterest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PoiController extends Controller
{
    private function resolveType(array $validated): string
    {
        if (! empty($validated['type'])) {
            return (string) $validated['type'];
        }

        if (! empty($validated['poi_type_id'])) {
            $poiType = PoiType::query()->find($validated['poi_type_id']);
            if ($poiType) {
                return (string) $poiType->name;
            }
        }

        return 'other';
    }

    private function authorize(Request $request, Neighborhood $neighborhood): void
    {
        if (! $request->user()->isAdmin() && $neighborhood->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }
    }

    public function index(Request $request, Neighborhood $neighborhood): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        $pois = $neighborhood->pointsOfInterest()
            ->with(['images', 'poiType'])
            ->get();

        return response()->json($pois);
    }

    public function store(Request $request, Neighborhood $neighborhood): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'poi_type_id' => 'nullable|exists:poi_types,id',
            'type' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'working_hours' => 'nullable|array',
        ]);

        $validated['type'] = $this->resolveType($validated);
        $validated['neighborhood_id'] = $neighborhood->id;

        $poi = PointOfInterest::create($validated);

        return response()->json($poi->load(['images', 'poiType']), 201);
    }

    public function update(Request $request, Neighborhood $neighborhood, PointOfInterest $poi): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        if ($poi->neighborhood_id !== $neighborhood->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'poi_type_id' => 'nullable|exists:poi_types,id',
            'type' => 'nullable|string|max:255',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'working_hours' => 'nullable|array',
        ]);

        if (array_key_exists('type', $validated) || array_key_exists('poi_type_id', $validated)) {
            $validated['type'] = $this->resolveType($validated + ['type' => $poi->type]);
        }

        $poi->update($validated);

        return response()->json($poi->load(['images', 'poiType']));
    }

    public function destroy(Request $request, Neighborhood $neighborhood, PointOfInterest $poi): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        if ($poi->neighborhood_id !== $neighborhood->id) {
            abort(404);
        }

        // Delete image files
        $directory = 'pois/' . $poi->id;
        Storage::disk('public')->deleteDirectory($directory);

        $poi->delete();

        return response()->json(['message' => 'POI deleted']);
    }

    public function uploadImage(Request $request, Neighborhood $neighborhood, PointOfInterest $poi): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        if ($poi->neighborhood_id !== $neighborhood->id) {
            abort(404);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_primary' => 'nullable|boolean',
        ]);

        $path = $request->file('image')->store('pois/' . $poi->id, 'public');
        $isPrimary = $request->boolean('is_primary', false);

        // If setting as primary, unset others
        if ($isPrimary) {
            $poi->images()->update(['is_primary' => false]);
        }

        // If first image, make it primary
        if ($poi->images()->count() === 0) {
            $isPrimary = true;
        }

        $image = $poi->images()->create([
            'path' => $path,
            'is_primary' => $isPrimary,
            'sort_order' => $poi->images()->max('sort_order') + 1,
        ]);

        return response()->json($image, 201);
    }

    public function deleteImage(Request $request, Neighborhood $neighborhood, PointOfInterest $poi, PoiImage $image): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        if ($poi->neighborhood_id !== $neighborhood->id || $image->point_of_interest_id !== $poi->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->path);

        $wasPrimary = $image->is_primary;
        $image->delete();

        // If deleted image was primary, make first remaining image primary
        if ($wasPrimary) {
            $firstImage = $poi->images()->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        return response()->json(['message' => 'Image deleted']);
    }

    public function setPrimaryImage(Request $request, Neighborhood $neighborhood, PointOfInterest $poi, PoiImage $image): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        if ($poi->neighborhood_id !== $neighborhood->id || $image->point_of_interest_id !== $poi->id) {
            abort(404);
        }

        $poi->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return response()->json($image);
    }
}
