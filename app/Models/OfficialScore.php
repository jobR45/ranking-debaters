<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OfficialScore extends Model
{

    use HasFactory;

    protected $fillable = [
        'debater_id',
        'etiquette',
        'knowledge',
        'logic',
        'victory',
        'total_score',
    ];

    public function debater()
    {
        return $this->belongsTo(Debater::class);
    }


}
