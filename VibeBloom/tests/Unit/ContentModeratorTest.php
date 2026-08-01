<?php

namespace Tests\Unit;

use App\Services\AI\ContentModerator;
use Tests\TestCase;

class ContentModeratorTest extends TestCase
{
    public function test_it_blocks_offensive_content_before_calling_the_api(): void
    {
        $result = app(ContentModerator::class)->review('Este lugar es una mierda', 'reseña');

        $this->assertFalse($result['allowed']);
        $this->assertNotEmpty($result['reason']);
    }

    public function test_it_blocks_promotional_links(): void
    {
        $result = app(ContentModerator::class)->review('Visita https://spam.example para ganar dinero', 'respuesta');

        $this->assertFalse($result['allowed']);
    }
}
