<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Admin only');

        return User::query()
            ->latest()
            ->paginate(20)
            ->through(function (User $user): array {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'roles' => $user->getRoleNames()->values(),
                    'created_at' => optional($user->created_at)->toISOString(),
                ];
            });
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Admin only');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'role' => 'required|in:super-admin,admin,user',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
        ]);

        $user->assignRole($data['role']);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'roles' => $user->getRoleNames()->values(),
            'created_at' => $user->created_at?->toISOString(),
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Admin only');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string',
            'role' => 'required|in:super-admin,admin,user',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            ...(filled($data['password'] ?? null) ? ['password' => $data['password']] : []),
        ]);

        $user->syncRoles([$data['role']]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'roles' => $user->getRoleNames()->values(),
            'created_at' => $user->created_at?->toISOString(),
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Admin only');
        abort_if($user->id === $request->user()->id, 403, 'O\'zingizni o\'chira olmaysiz');

        $user->delete();

        return response()->json(['message' => 'Foydalanuvchi o\'chirildi']);
    }
}

