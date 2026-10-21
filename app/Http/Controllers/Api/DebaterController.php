<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AudienceReview;
use App\Models\Debater;
use Illuminate\Http\Request;

class DebaterController extends Controller
{
    public function index()
    {
        $debaters = Debater::with(['officialScore', 'audienceReviews'])
            ->get()
            ->map(function ($debater) {
                // Calculate average audience score
                $audienceAvg = $debater->audienceReviews->avg('total_score');
                $debater->audience_score = $audienceAvg ? round($audienceAvg) : null;
                return $debater;
            })
            ->sortByDesc('officialScore.total_score')
            ->values();

        return response()->json($debaters);
    }

    // Submit audience review
    public function storeAudienceReview(Request $request, Debater $debater)
    {
        $user = $request->user();
        $existingReview = $debater->audienceReviews()
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'لقد قمت بتقييم هذا المناظر مسبقاً. لا يمكنك إرسال أكثر من تقييم.'
            ], 422);
        }

        $validated = $request->validate([
            'etiquette' => 'required|integer|min:0|max:25',
            'knowledge' => 'required|integer|min:0|max:25',
            'logic' => 'required|integer|min:0|max:25',
            'victory' => 'required|integer|min:0|max:25',
            'comment' => 'nullable|string|max:500',
        ]);

        $validated['total_score'] = $validated['etiquette']
            + $validated['knowledge']
            + $validated['logic']
            + $validated['victory'];
        $validated['user_id'] = $user->id;
        $validated['reviewer_name'] = $user->name;

        $debater->audienceReviews()->create($validated);

        return response()->json(['message' => 'تم تسجيل تقييمك بنجاح!']);
    }

    public function audienceReviews(Request $request, Debater $debater)
    {
        $currentUserId = $request->user('sanctum')?->getAuthIdentifier();
        $reviewsQuery = $debater->audienceReviews();

        if ($currentUserId !== null) {
            $reviewsQuery->orderByRaw('CASE WHEN user_id = ? THEN 0 ELSE 1 END', [$currentUserId]);
        }

        $reviews = $reviewsQuery
            ->latest('created_at')
            ->latest('id')
            ->paginate(5);

        $reviews->getCollection()->transform(function (AudienceReview $review) use ($currentUserId) {
            $review->setAttribute(
                'is_owner',
                $currentUserId !== null && (int) $currentUserId === (int) $review->user_id,
            );

            return $review;
        });

        $averageScore = $debater->audienceReviews()->avg('total_score');
        $ownReview = $currentUserId === null
            ? null
            : $debater->audienceReviews()->where('user_id', $currentUserId)->first();

        $ownReview?->setAttribute('is_owner', true);

        return response()->json(array_merge($reviews->toArray(), [
            'average_score' => $averageScore === null ? null : (int) round($averageScore),
            'own_review' => $ownReview,
        ]));
    }

    public function updateAudienceReview(Request $request, AudienceReview $review)
    {
        abort_unless($review->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'etiquette' => 'required|integer|min:0|max:25',
            'knowledge' => 'required|integer|min:0|max:25',
            'logic' => 'required|integer|min:0|max:25',
            'victory' => 'required|integer|min:0|max:25',
            'comment' => 'nullable|string|max:500',
        ]);

        $validated['total_score'] = $validated['etiquette']
            + $validated['knowledge']
            + $validated['logic']
            + $validated['victory'];

        $review->update($validated);

        return response()->json(['message' => 'تم تحديث تقييمك بنجاح!']);
    }

    public function destroyAudienceReview(Request $request, AudienceReview $review)
    {
        abort_unless($review->user_id === $request->user()->id, 404);

        $review->delete();

        return response()->json(['message' => 'تم حذف تقييمك بنجاح!']);
    }

    // Fetch single debater with all related details
    public function show($id)
    {
        $debater = Debater::with(['officialScore', 'debaterReviews'])
            ->findOrFail($id);

        return response()->json($debater);
    }
}
