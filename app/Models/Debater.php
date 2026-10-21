<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class Debater extends Model
{


    use HasFactory;

    protected $fillable = [
        'name_ar',
        'bio',
        'photo_url',
        'country_code',
        'latitude',
        'longitude',
        'social_links',
        'user_id',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function officialScore()
    {
        return $this->hasOne(OfficialScore::class);
    }

    public function audienceReviews()
    {
        return $this->hasMany(AudienceReview::class);
    }

    public function debaterReviews()
    {
        return $this->hasMany(DebaterReview::class);
    }

}
