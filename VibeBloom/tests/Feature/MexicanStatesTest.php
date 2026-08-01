<?php

namespace Tests\Feature;

use App\Models\MexicanState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MexicanStatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_contains_the_32_mexican_states(): void
    {
        $this->assertSame(32, MexicanState::query()->count());
        $this->assertDatabaseHas('mexican_states', ['name' => 'Querétaro', 'code' => 'QRO']);
    }
}
