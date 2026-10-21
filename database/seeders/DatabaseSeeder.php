<?php

namespace Database\Seeders;

use App\Models\Debater;
use App\Models\DebaterReview;
use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call([
            DebaterSeeder::class,

        ]);

        // Create Platform Admin
        User::create([
            'name' => 'مدير المنصة',
            'email' => 'admin@ranking.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create Test Audience User
        User::create([
            'name' => 'متابع تجريبي',
            'email' => 'user@ranking.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);
        DebaterReview::truncate();

        DebaterReview::create([
            'debater_id' => '1',
            'title' => 'تحليل شامل لأداء حسن عيسى في مناظرة الذكاء الاصطناعي',
            'written_review' => "**المحاور الأساسية للمناظرة:**\n\n1. **الفصاحة واللغة:** أظهر تمكناً لغوياً عالياً واستخداماً دقيقاً للمصطلحات العلمية.\n2. **البناء المنطقي:** اعتمد على تسلسل استقرائي متماسك مما يصعب على الخصم تفكيك حججه.\n3. **إدارة الوقت:** استغل الدقائق المخصصة له بالكامل دون استعجال أو إطالة.",
            'video_url' => 'https://www.youtube.com/watch?v=example1',
            'likes_count' => 24,
        ]);

        DebaterReview::create([
            'debater_id' => '2',
            'title' => 'قراءة نقدية في أسلوب زين خير الله في مناظرات الاقتصاد',
            'written_review' => "**نقاط القوة والتحليل:**\n\n1. **سرعة البديهة:** الرد الفوري على المغالطات المنطقية للطرف الآخر بأسلوب هادئ ومقنع.\n2. **الاستدلال بالبيانات:** الاعتماد على الأرقام والإحصائيات الحديثة لدعم الموقف.\n3. **الضبط الانفعالي:** الحفاظ على الهدوء والالتزام التام بآداب النقاش حتى تحت الضغط.",
            'video_url' => 'https://www.youtube.com/watch?v=example2',
            'likes_count' => 18,
        ]);

    }
}
