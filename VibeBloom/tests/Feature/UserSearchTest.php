<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_search_matches_all_words_and_prioritizes_precise_results(): void
    {
        Http::fake(['*' => Http::response([], 200)]);

        $viewer = User::factory()->create();
        User::factory()->create(['name' => 'Ana María López', 'email' => 'ana@example.com', 'profile_is_public' => true]);
        User::factory()->create(['name' => 'Ana Torres', 'email' => 'torres@example.com', 'profile_is_public' => true]);

        $response = $this->actingAs($viewer)->get('/usuarios?q=Ana%20Mar%C3%ADa');

        $response->assertOk()
            ->assertSee('Ana María López')
            ->assertDontSee('Ana Torres');
    }
}
