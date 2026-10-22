<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    /**
     * List all platform users
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 10), 1), 100);
        $users = User::latest()
            ->paginate($perPage, ['id', 'name', 'email', 'role', 'created_at'])
            ->withQueryString();

        return response()->json($users);
    }

    /**
     * Create a new Admin or Super Admin user (Super Admin Only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,admin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'تم إنشاء حساب المسؤول بنجاح.',
            'user' => $user,
        ], 201);
    }

    /**
     * Update user role (Super Admin Only)
     */
    public function updateRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:super_admin,admin,user',
        ]);

        $user = User::findOrFail($id);

        // Prevent self-demotion for Super Admin
        if ($user->id === $request->user()->id && $validated['role'] !== 'super_admin') {
            return response()->json(['message' => 'لا يمكنك إلغاء صلاحية مدير النظام الرئيسي عن نفسك.'], 422);
        }

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'message' => 'تم تغيير دور المستخدم بنجاح.',
            'user' => $user,
        ]);
    }

    /**
     * Delete user account (Super Admin Only)
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'لا يمكنك حذف حسابك الحالي.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'تم حذف الحساب بنجاح.']);
    }
}
