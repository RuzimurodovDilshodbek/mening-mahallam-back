<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PoiType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PoiTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(PoiType::orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:poi_types,name',
        ]);

        $type = PoiType::create($validated);

        return response()->json($type, 201);
    }
}
