<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DebaterReview;
use Illuminate\Http\Request;

class OfficialReviewAdminController extends Controller
{
   /**
     * Create or update official debater review
     */
    public function storeOrUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'written_review' => 'required|string',
            'video_url' => 'nullable|url',
        ]);

        $review = DebaterReview::updateOrCreate(
            ['debater_id' => $id],
            [
                'title' => $validated['title'],
                'written_review' => $validated['written_review'],
                'video_url' => $validated['video_url'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'تم حفظ المراجعة الرسمية بنجاح.',
            'review' => $review
        ]);
    }
}
