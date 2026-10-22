<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AudienceReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AudienceReviewAdminController extends Controller
{
    /**
     * List audience reviews (optionally filtered by debater_id)
     */
    public function index(Request $request): JsonResponse
    {
        $query = AudienceReview::with(['debater', 'user']);

        if ($request->has('debater_id') && $request->debater_id) {
            $query->where('debater_id', $request->debater_id);
        }

        $averageScore = (clone $query)->avg('total_score');
        $perPage = min(max($request->integer('per_page', 10), 1), 100);
        $reviews = $query->latest()->paginate($perPage)->withQueryString();

        return response()->json(array_merge($reviews->toArray(), [
            'average_score' => $averageScore === null ? null : (float) $averageScore,
        ]));
    }

    /**
     * Delete inappropriate audience review
     */
    public function destroy($id)
    {
        $review = AudienceReview::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'تم حذف تقييم الجمهور بنجاح.']);
    }
}
