<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Admin only');
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        return Neighborhood::query()
            ->with('user:id,name,email')
            ->latest()
            ->paginate(20);
    }

    public function verify(Request $request, Neighborhood $neighborhood)
    {
        $this->ensureAdmin($request);

        $neighborhood->update([
            'is_verified' => true,
            'status' => 'published',
        ]);

        return response()->json([
            'message' => 'Neighborhood verified',
        ]);
    }
}

