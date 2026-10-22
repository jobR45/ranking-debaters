<?php

namespace Tests\Feature\Api;

use App\Models\AudienceReview;
use App\Models\Debater;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_debaters_are_paginated_for_admins(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        foreach (range(1, 11) as $index) {
            Debater::create(['name_ar' => "Debater {$index}"]);
        }

        $this->getJson('/api/admin/debaters')
            ->assertOk()
            ->assertJsonPath('total', 11)
            ->assertJsonPath('per_page', 10)
            ->assertJsonCount(10, 'data');

        $this->getJson('/api/admin/debaters?page=2')
            ->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonCount(1, 'data');
    }

    public function test_audience_reviews_remain_filtered_and_paginated(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $filteredDebater = Debater::create(['name_ar' => 'Filtered debater']);
        $otherDebater = Debater::create(['name_ar' => 'Other debater']);

        foreach (range(1, 11) as $index) {
            AudienceReview::create([
                'debater_id' => $filteredDebater->id,
                'reviewer_name' => "Reviewer {$index}",
                'total_score' => 80,
            ]);
        }

        AudienceReview::create([
            'debater_id' => $otherDebater->id,
            'reviewer_name' => 'Other reviewer',
            'total_score' => 20,
        ]);

        $this->getJson("/api/admin/audience-reviews?debater_id={$filteredDebater->id}&page=2")
            ->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('total', 11)
            ->assertJsonPath('average_score', 80)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.debater_id', $filteredDebater->id);
    }

    public function test_users_are_paginated_for_admins(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        User::factory()->count(10)->create();

        $this->getJson('/api/admin/users')
            ->assertOk()
            ->assertJsonPath('total', 11)
            ->assertJsonPath('per_page', 10)
            ->assertJsonCount(10, 'data');

        $this->getJson('/api/admin/users?page=2')
            ->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonCount(1, 'data');
    }
}
