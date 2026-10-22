<?php

namespace Tests\Feature\Api;

use App\Models\AudienceReview;
use App\Models\Debater;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminDebaterDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_debater_with_one_score_and_one_review(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->postJson('/api/admin/debaters', $this->validDebaterDetails())
            ->assertCreated()
            ->assertJsonPath('debater.name_ar', 'مناظر تجريبي')
            ->assertJsonPath('debater.official_score.total_score', 80)
            ->assertJsonPath('debater.debater_review.title', 'مراجعة تجريبية');

        $debater = Debater::firstOrFail();
        $this->assertDatabaseCount('official_scores', 1);
        $this->assertDatabaseCount('debater_reviews', 1);
        $this->assertSame($debater->id, $debater->fresh()->debaterReview->debater_id);
    }

    public function test_admin_can_update_debater_details_without_creating_another_score_or_review(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $created = $this->postJson('/api/admin/debaters', $this->validDebaterDetails())
            ->assertCreated()
            ->json('debater');

        $updatedDetails = [
            ...$this->validDebaterDetails(),
            'name_ar' => 'اسم محدث',
            'etiquette' => 25,
            'written_review' => 'مراجعة محدثة',
        ];

        $this->putJson("/api/admin/debaters/{$created['id']}", $updatedDetails)
            ->assertOk()
            ->assertJsonPath('debater.name_ar', 'اسم محدث')
            ->assertJsonPath('debater.official_score.total_score', 85)
            ->assertJsonPath('debater.debater_review.written_review', 'مراجعة محدثة');

        $this->assertDatabaseCount('official_scores', 1);
        $this->assertDatabaseCount('debater_reviews', 1);
        $this->assertDatabaseHas('debater_reviews', [
            'debater_id' => $created['id'],
            'written_review' => 'مراجعة محدثة',
        ]);
    }

    public function test_admin_can_update_basic_information_without_overwriting_scores_or_review(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $created = $this->postJson('/api/admin/debaters', $this->validDebaterDetails())
            ->assertCreated()
            ->json('debater');

        $this->putJson("/api/admin/debaters/{$created['id']}", ['name_ar' => 'اسم جديد'])
            ->assertOk()
            ->assertJsonPath('debater.name_ar', 'اسم جديد')
            ->assertJsonPath('debater.official_score.total_score', 80)
            ->assertJsonPath('debater.debater_review.title', 'مراجعة تجريبية');

        $this->assertDatabaseCount('official_scores', 1);
        $this->assertDatabaseCount('debater_reviews', 1);
    }

    public function test_admin_detail_includes_the_debaters_audience_average_and_single_review(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $debater = Debater::create(['name_ar' => 'مناظر تجريبي']);
        $debater->debaterReview()->create([
            'title' => 'مراجعة',
            'written_review' => 'النص',
        ]);
        AudienceReview::create([
            'debater_id' => $debater->id,
            'reviewer_name' => 'مقيّم',
            'total_score' => 84,
        ]);

        $this->getJson("/api/admin/debaters/{$debater->id}")
            ->assertOk()
            ->assertJsonPath('audience_score', 84)
            ->assertJsonPath('debater_review.title', 'مراجعة')
            ->assertJsonMissingPath('debater_reviews');

        $this->getJson("/api/debaters/{$debater->id}")
            ->assertOk()
            ->assertJsonPath('audience_score', 84)
            ->assertJsonPath('debater_review.title', 'مراجعة')
            ->assertJsonMissingPath('debater_reviews');
    }

    public function test_invalid_combined_creation_does_not_create_a_partial_debater(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->postJson('/api/admin/debaters', [
            ...$this->validDebaterDetails(),
            'written_review' => '',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('written_review');

        $this->assertDatabaseCount('debaters', 0);
        $this->assertDatabaseCount('official_scores', 0);
        $this->assertDatabaseCount('debater_reviews', 0);
    }

    private function validDebaterDetails(): array
    {
        return [
            'name_ar' => 'مناظر تجريبي',
            'country_code' => 'EG',
            'photo_url' => 'https://example.com/debater.jpg',
            'bio' => 'نبذة تجريبية',
            'etiquette' => 20,
            'knowledge' => 20,
            'logic' => 20,
            'victory' => 20,
            'title' => 'مراجعة تجريبية',
            'written_review' => 'نص المراجعة',
            'video_url' => null,
        ];
    }
}
