<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DebaterReview;
use Illuminate\Http\Request;

class DebaterReviewController extends Controller
{
   // Fetch all debate reviews with debater info
    public function index()
    {
        $reviews = DebaterReview::with('debater')
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    // Create a new debate review
    public function store(Request $request)
    {
        $validated = $request->validate([
            'debater_id' => 'required|exists:debaters,id',
            'title' => 'required|string|max:255',
            'written_review' => 'required|string',
            'video_url' => 'nullable|url',
        ]);

        $review = DebaterReview::create($validated);

        return response()->json([
            'message' => 'تم إضافة مراجعة المناظرة بنجاح',
            'review' => $review->load('debater')
        ], 210);
    }

    // Like a debate review post
    public function like($id)
    {
        $review = DebaterReview::findOrFail($id);
        $review->increment('likes_count');

        return response()->json([
            'message' => 'تم تسجيل الإعجاب',
            'likes_count' => $review->likes_count
        ]);
    }
}
