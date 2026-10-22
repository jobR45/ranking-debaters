<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudienceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'debater_id',
        'user_id',
        'reviewer_name',
        'etiquette',
        'knowledge',
        'logic',
        'victory',
        'total_score',
        'comment',
        'approved',
    ];

    protected $hidden = [
        'user_id',
    ];

    public function debater()
    {
        return $this->belongsTo(Debater::class);
    }

    /**
     * Relationship to the User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
