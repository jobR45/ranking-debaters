<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function officialScore(): HasOne
    {
        return $this->hasOne(OfficialScore::class);
    }

    public function debaterReview(): HasOne
    {
        return $this->hasOne(DebaterReview::class);
    }

    public function audienceReviews(): HasMany
    {
        return $this->hasMany(AudienceReview::class);
    }
}
