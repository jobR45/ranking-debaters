<?php

namespace Tests\Feature\Api;

use App\Models\AudienceReview;
use App\Models\Debater;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AudienceReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_creates_review_and_appears_as_owner(): void
    {
        $user = $this->createUser('reviewer@example.com');
        $debater = $this->createDebater();
        Sanctum::actingAs($user);

        $this->postJson("/api/debaters/{$debater->id}/reviews", $this->validReview())
            ->assertOk()
            ->assertJsonPath('message', 'تم تسجيل تقييمك بنجاح!');

        $this->assertDatabaseHas('audience_reviews', [
            'debater_id' => $debater->id,
            'user_id' => $user->id,
            'total_score' => 80,
        ]);

        $this->getJson("/api/debaters/{$debater->id}/reviews")
            ->assertOk()
            ->assertJsonPath('data.0.is_owner', true)
            ->assertJsonMissingPath('own_review.user_id');
    }

    public function test_reviews_are_paginated_and_the_owners_review_is_always_available(): void
    {
        $owner = $this->createUser('owner@example.com');
        $otherUser = $this->createUser('other@example.com');
        $debater = $this->createDebater();
        $ownReview = $this->createReview($debater, $owner);

        for ($index = 0; $index < 5; $index++) {
            $this->createReview($debater, $otherUser);
        }

        Sanctum::actingAs($owner);

        $this->getJson("/api/debaters/{$debater->id}/reviews")
            ->assertOk()
            ->assertJsonPath('total', 6)
            ->assertJsonPath('per_page', 5)
            ->assertJsonPath('data.0.id', $ownReview->id)
            ->assertJsonPath('data.0.is_owner', true)
            ->assertJsonPath('own_review.id', $ownReview->id)
            ->assertJsonMissingPath('data.0.user_id');
    }

    public function test_owner_can_update_review_and_total_score_is_recalculated(): void
    {
        $user = $this->createUser('reviewer@example.com');
        $debater = $this->createDebater();
        $review = $this->createReview($debater, $user);
        Sanctum::actingAs($user);

        $this->putJson("/api/audience-reviews/{$review->id}", [
            ...$this->validReview(),
            'etiquette' => 25,
            'comment' => 'Updated comment',
        ])->assertOk();

        $this->assertDatabaseHas('audience_reviews', [
            'id' => $review->id,
            'user_id' => $user->id,
            'total_score' => 85,
            'comment' => 'Updated comment',
        ]);
    }

    public function test_owner_can_delete_review(): void
    {
        $user = $this->createUser('reviewer@example.com');
        $debater = $this->createDebater();
        $review = $this->createReview($debater, $user);
        Sanctum::actingAs($user);

        $this->deleteJson("/api/audience-reviews/{$review->id}")
            ->assertOk()
            ->assertJsonPath('message', 'تم حذف تقييمك بنجاح!');

        $this->assertDatabaseMissing('audience_reviews', ['id' => $review->id]);
    }

    public function test_non_owner_cannot_update_or_delete_a_review(): void
    {
        $owner = $this->createUser('owner@example.com');
        $otherUser = $this->createUser('other@example.com');
        $debater = $this->createDebater();
        $review = $this->createReview($debater, $owner);
        Sanctum::actingAs($otherUser);

        $this->putJson("/api/audience-reviews/{$review->id}", $this->validReview())
            ->assertNotFound();
        $this->deleteJson("/api/audience-reviews/{$review->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('audience_reviews', [
            'id' => $review->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_submit_a_review(): void
    {
        $debater = $this->createDebater();

        $this->postJson("/api/debaters/{$debater->id}/reviews", $this->validReview())
            ->assertUnauthorized();

        $this->assertDatabaseCount('audience_reviews', 0);
    }

    public function test_invalid_review_scores_are_rejected_without_persisting(): void
    {
        $user = $this->createUser('reviewer@example.com');
        $debater = $this->createDebater();
        Sanctum::actingAs($user);

        $this->postJson("/api/debaters/{$debater->id}/reviews", [
            ...$this->validReview(),
            'etiquette' => 26,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('etiquette');

        $this->assertDatabaseCount('audience_reviews', 0);
    }

    private function createUser(string $email): User
    {
        return User::create([
            'name' => 'Review User',
            'email' => $email,
            'password' => Hash::make('test-password'),
        ]);
    }

    private function createDebater(): Debater
    {
        return Debater::create(['name_ar' => 'مناظر تجريبي']);
    }

    private function createReview(Debater $debater, User $user): AudienceReview
    {
        return $debater->audienceReviews()->create([
            'user_id' => $user->id,
            'reviewer_name' => $user->name,
            'etiquette' => 20,
            'knowledge' => 20,
            'logic' => 20,
            'victory' => 20,
            'total_score' => 80,
            'comment' => 'A review',
        ]);
    }

    private function validReview(): array
    {
        return [
            'etiquette' => 20,
            'knowledge' => 20,
            'logic' => 20,
            'victory' => 20,
            'comment' => 'A review',
        ];
    }
}
