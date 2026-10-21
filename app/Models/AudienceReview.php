<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
