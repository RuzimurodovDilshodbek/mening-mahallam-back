<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\Neighborhood;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    private function authorize(Request $request, Neighborhood $neighborhood): void
    {
        if (! $request->user()->isAdmin() && $neighborhood->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }
    }

    public function index(Request $request, Neighborhood $neighborhood): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        return response()->json($neighborhood->cameras);
    }

    public function store(Request $request, Neighborhood $neighborhood): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,maintenance',
        ]);

        $validated['neighborhood_id'] = $neighborhood->id;
        $validated['status'] = $validated['status'] ?? 'active';

        $camera = Camera::create($validated);

        return response()->json($camera, 201);
    }

    public function update(Request $request, Neighborhood $neighborhood, Camera $camera): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        if ($camera->neighborhood_id !== $neighborhood->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,maintenance',
        ]);

        $camera->update($validated);

        return response()->json($camera);
    }

    public function destroy(Request $request, Neighborhood $neighborhood, Camera $camera): JsonResponse
    {
        $this->authorize($request, $neighborhood);

        if ($camera->neighborhood_id !== $neighborhood->id) {
            abort(404);
        }

        $camera->delete();

        return response()->json(['message' => 'Camera deleted']);
    }
}
