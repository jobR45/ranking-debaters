<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Debater;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DebaterAdminController extends Controller
{
    /**
     * List all debaters for admin management
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 10), 1), 100);
        $debaters = Debater::with(['officialScore', 'debaterReview'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json(array_merge($debaters->toArray(), [
            'official_reviews_count' => Debater::has('debaterReview')->count(),
        ]));
    }

    public function show(Debater $debater): JsonResponse
    {
        $debater->load(['officialScore', 'debaterReview']);
        $debater->loadAvg('audienceReviews', 'total_score');
        $debater->setAttribute(
            'audience_score',
            $debater->audience_reviews_avg_total_score === null
                ? null
                : (int) round($debater->audience_reviews_avg_total_score),
        );
        $debater->makeHidden('audience_reviews_avg_total_score');

        return response()->json($debater);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->debaterDetailsRules());

        $debater = DB::transaction(function () use ($validated): Debater {
            $debater = Debater::create(Arr::only($validated, [
                'name_ar',
                'bio',
                'country_code',
                'photo_url',
            ]));

            $debater->officialScore()->create([
                ...Arr::only($validated, ['etiquette', 'knowledge', 'logic', 'victory']),
                'total_score' => $this->totalScore($validated),
            ]);

            $debater->debaterReview()->create(Arr::only($validated, [
                'title',
                'written_review',
                'video_url',
            ]));

            return $debater->load(['officialScore', 'debaterReview']);
        });

        return response()->json([
            'message' => 'تم إضافة المناظر ومراجعته بنجاح.',
            'debater' => $debater,
        ], 201);
    }

    public function update(Request $request, Debater $debater): JsonResponse
    {
        $validated = $request->validate([
            'name_ar' => 'sometimes|required|string|max:255',
            'bio' => 'sometimes|nullable|string',
            'country_code' => 'sometimes|required|string|size:2',
            'photo_url' => 'sometimes|nullable|url|max:2048',
            'etiquette' => 'sometimes|required_with:etiquette,knowledge,logic,victory|integer|min:0|max:25',
            'knowledge' => 'sometimes|required_with:etiquette,knowledge,logic,victory|integer|min:0|max:25',
            'logic' => 'sometimes|required_with:etiquette,knowledge,logic,victory|integer|min:0|max:25',
            'victory' => 'sometimes|required_with:etiquette,knowledge,logic,victory|integer|min:0|max:25',
            'title' => 'sometimes|required_with:title,written_review|string|max:255',
            'written_review' => 'sometimes|required_with:title,written_review|string',
            'video_url' => 'sometimes|nullable|url|max:2048',
        ]);

        $debater = DB::transaction(function () use ($debater, $validated): Debater {
            $debaterAttributes = Arr::only($validated, [
                'name_ar',
                'bio',
                'country_code',
                'photo_url',
            ]);

            if ($debaterAttributes !== []) {
                $debater->update($debaterAttributes);
            }

            $scoreAttributes = Arr::only($validated, ['etiquette', 'knowledge', 'logic', 'victory']);
            if ($scoreAttributes !== []) {
                $debater->officialScore()->updateOrCreate([], [
                    ...$scoreAttributes,
                    'total_score' => $this->totalScore($validated),
                ]);
            }

            $reviewAttributes = Arr::only($validated, [
                'title',
                'written_review',
                'video_url',
            ]);

            if ($reviewAttributes !== []) {
                if (
                    ! $debater->debaterReview()->exists()
                    && (! isset($reviewAttributes['title']) || ! isset($reviewAttributes['written_review']))
                ) {
                    Validator::make($reviewAttributes, [
                        'title' => 'required|string|max:255',
                        'written_review' => 'required|string',
                    ])->validate();
                }

                $debater->debaterReview()->updateOrCreate([], $reviewAttributes);
            }

            return $debater->load(['officialScore', 'debaterReview']);
        });

        return response()->json([
            'message' => 'تم تحديث بيانات المناظر ومراجعته بنجاح.',
            'debater' => $debater,
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function debaterDetailsRules(): array
    {
        return [
            'name_ar' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'country_code' => 'required|string|size:2',
            'photo_url' => 'nullable|url|max:2048',
            'etiquette' => 'required|integer|min:0|max:25',
            'knowledge' => 'required|integer|min:0|max:25',
            'logic' => 'required|integer|min:0|max:25',
            'victory' => 'required|integer|min:0|max:25',
            'title' => 'required|string|max:255',
            'written_review' => 'required|string',
            'video_url' => 'nullable|url|max:2048',
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function totalScore(array $validated): int
    {
        return $validated['etiquette']
            + $validated['knowledge']
            + $validated['logic']
            + $validated['victory'];
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
