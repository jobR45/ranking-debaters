<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficialScore;
use Illuminate\Http\Request;

class OfficialScoreAdminController extends Controller
{
 /**
     * Store or update official criteria scores (out of 25 each)
     */
    public function storeOrUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'etiquette' => 'required|integer|min:0|max:25',
            'knowledge' => 'required|integer|min:0|max:25',
            'logic' => 'required|integer|min:0|max:25',
            'victory' => 'required|integer|min:0|max:25',
        ]);

        $totalScore = $validated['etiquette'] + $validated['knowledge'] + $validated['logic'] + $validated['victory'];

        $score = OfficialScore::updateOrCreate(
            ['debater_id' => $id],
            [
                'etiquette' => $validated['etiquette'],
                'knowledge' => $validated['knowledge'],
                'logic' => $validated['logic'],
                'victory' => $validated['victory'],
                'total_score' => $totalScore,
            ]
        );

        return response()->json([
            'message' => 'تم تحديث النتيجة الرسمية بنجاح.',
            'score' => $score
        ]);
    }
}
