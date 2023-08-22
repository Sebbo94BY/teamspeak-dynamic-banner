<?php

namespace Tests\Unit;

use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use Illuminate\Database\Eloquent\Collection;
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

        BannerTemplate::factory()->for(
            Banner::factory()->for(
                Instance::factory()->create()
            )->create()
        )->for($template)->create();

        $this->assertIsBool($template->isUsedByBannerId(1));
        $this->assertIsBool($template->isEnabledForBannerId(1));
        $this->assertInstanceOf(Collection::class, $template->banner_templates());
    }
}
