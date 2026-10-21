<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AudienceReview;
use Illuminate\Http\Request;

class AudienceReviewAdminController extends Controller
{
    /**
     * List all audience reviews for moderation
     */
    public function index()
    {
        $reviews = AudienceReview::with(['debater', 'user'])->latest()->get();
        return response()->json($reviews);
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
