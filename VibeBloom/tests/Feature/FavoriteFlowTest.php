<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FavoriteFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_save_a_place_as_favorite(): void
    {
        Http::fake([
            '*/favorites/toggle' => Http::response([
                'message' => 'Lugar guardado en tus favoritos.',
                'is_favorite' => true,
                'place_id' => 12,
            ]),
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->withSession(['access_token' => 'valid-test-token'])
            ->from('/dashboard')
            ->post(route('favorite.toggle', 12));

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success', 'Lugar guardado en tus favoritos.');

        Http::assertSent(fn ($request) =>
            $request->url() === config('services.fastapi.url').'/favorites/toggle'
            && $request['place_id'] === 12
            && $request->hasHeader('Authorization', 'Bearer valid-test-token')
        );
    }

    public function test_expired_api_session_sends_user_to_login_with_a_clear_message(): void
    {
        Http::fake([
            '*/favorites/toggle' => Http::response(['detail' => 'Token inválido'], 401),
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->withSession(['access_token' => 'expired-test-token'])
            ->post(route('favorite.toggle', 12));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Tu sesión venció. Inicia sesión nuevamente para continuar.');
        $response->assertSessionMissing('access_token');
    }
}
