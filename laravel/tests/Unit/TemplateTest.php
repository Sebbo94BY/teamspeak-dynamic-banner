<?php

namespace Tests\Unit;

use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_can_be_created()
    {
        $template = Template::factory()->create();

        $this->assertModelExists($template);
    }

    public function test_model_relationships()
    {
        $template = Template::factory()->create();

        $this->assertIsBool($template->isUsedByBannerId(1));
        $this->assertIsBool($template->isEnabledForBannerId(1));
    }
}
