<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Debater;
use Illuminate\Http\Request;

class DebaterAdminController extends Controller
{
    /**
     * List all debaters for admin management
     */
    public function index()
    {
        $debaters = Debater::with(['officialScore'])->latest()->get();
        return response()->json($debaters);
    }

    /**
     * Add a new debater
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'country_code' => 'required|string|size:2',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $debater = Debater::create($validated);

        return response()->json([
            'message' => 'تم إضافة المناظر بنجاح.',
            'debater' => $debater
        ], 201);
    }

    /**
     * Update an existing debater
     */
    public function update(Request $request, $id)
    {
        $debater = Debater::findOrFail($id);

        $validated = $request->validate([
            'name_ar' => 'sometimes|required|string|max:255',
            'bio' => 'nullable|string',
            'country_code' => 'sometimes|required|string|size:2',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $debater->update($validated);

        return response()->json([
            'message' => 'تم تحديث بيانات المناظر بنجاح.',
            'debater' => $debater
        ]);
    }

    /**
     * Delete a debater profile
     */
    public function destroy($id)
    {
        $debater = Debater::findOrFail($id);
        $debater->delete();

        return response()->json(['message' => 'تم حذف المناظر بنجاح.']);
    }
}
