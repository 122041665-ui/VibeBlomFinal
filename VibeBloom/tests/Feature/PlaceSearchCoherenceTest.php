<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlaceSearchCoherenceTest extends TestCase
{
    private array $places = [
        [
            'id' => 1,
            'name' => 'Bar La Estrella',
            'city' => 'Puebla',
            'type' => 'BAR',
            'description' => 'Cocteles y música en vivo',
            'address' => 'Avenida Reforma 10',
            'price' => 250,
        ],
        [
            'id' => 2,
            'name' => 'Café del Embarcadero',
            'city' => 'Veracruz',
            'type' => 'CAFETERIA',
            'description' => 'Café frente al mar',
            'address' => 'Paseo del Malecón 20',
            'price' => 180,
        ],
        [
            'id' => 3,
            'name' => 'Museo Amparo',
            'city' => 'Puebla',
            'type' => 'MUSEO',
            'description' => 'Colección de arte mexicano',
            'address' => 'Centro Histórico',
            'price' => 100,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*/places' => Http::response($this->places),
        ]);
    }

    public function test_search_does_not_match_a_term_hidden_inside_an_unrelated_word(): void
    {
        $response = $this->get(route('places.index', ['buscar' => 'bar']));

        $response->assertOk()
            ->assertSee('Bar La Estrella')
            ->assertDontSee('Café del Embarcadero');
    }

    public function test_all_meaningful_words_must_match_the_place(): void
    {
        $response = $this->get(route('places.index', ['buscar' => 'museo en Puebla']));

        $response->assertOk()
            ->assertSee('Museo Amparo')
            ->assertDontSee('Bar La Estrella')
            ->assertDontSee('Café del Embarcadero');
    }

    public function test_one_character_or_connector_only_queries_return_no_results(): void
    {
        $this->get(route('places.index', ['buscar' => 'a']))
            ->assertOk()
            ->assertDontSee('Bar La Estrella')
            ->assertDontSee('Museo Amparo');

        $this->get(route('places.index', ['buscar' => 'de la']))
            ->assertOk()
            ->assertDontSee('Café del Embarcadero');
    }
}
