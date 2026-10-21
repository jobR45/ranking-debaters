<?php

namespace Database\Seeders;

use App\Models\Debater;
use App\Models\OfficialScore;
use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DebaterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       
        // 1. Debater: حسن عيسى
        $debater1 = Debater::create([
            'name_ar' => 'حسن عيسى',
            'bio' => 'مناظر متمرس في القضايا الاجتماعية والسياسية',
            'photo_url' => null,
            'country_code' => 'SA', // Saudi Arabia (example location for map)
            'latitude' => 24.7136,
            'longitude' => 46.6753,
        ]);

        OfficialScore::create([
            'debater_id' => $debater1->id,
            'etiquette' => 16,  // آداب النقاش
            'knowledge' => 21,  // المعرفة
            'logic' => 15,      // المنطق
            'victory' => 19,    // الفوز
            'total_score' => 71, // 71/100
        ]);

        // 2. Debater: زين خير الله
        $debater2 = Debater::create([
            'name_ar' => 'زين خير الله',
            'bio' => 'مناظر متخصص في المناظرات الفلسفية والفكرية',
            'photo_url' => null,
            'country_code' => 'JO', // Jordan (example location for map)
            'latitude' => 31.9454,
            'longitude' => 35.9284,
        ]);

        OfficialScore::create([
            'debater_id' => $debater2->id,
            'etiquette' => 9,   // آداب النقاش
            'knowledge' => 13,  // المعرفة
            'logic' => 11,      // المنطق
            'victory' => 13,    // الفوز
            'total_score' => 46, // 46/100
        ]);


    }

}
