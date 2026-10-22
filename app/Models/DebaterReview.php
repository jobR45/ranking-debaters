<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebaterReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'debater_id',
        'title',
        'written_review',
        'video_url',
        'likes_count',
    ];

    public function debater(): BelongsTo
    {
        return $this->belongsTo(Debater::class);
    }
}
