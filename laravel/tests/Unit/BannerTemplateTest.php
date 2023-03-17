<?php

namespace Tests\Unit;

use App\Models\BannerTemplate;
use Tests\TestCase;

class BannerTemplateTest extends TestCase
{
    public function test_model_can_be_created()
    {
        $banner_template = new BannerTemplate([
            'banner_id' => 1,
            'template_id' => 2,
            'enabled' => true,
        ]);

        $this->assertEquals(1, $banner_template->banner_id);
        $this->assertEquals(2, $banner_template->template_id);
        $this->assertEquals(true, $banner_template->enabled);
    }

    public function test_casts_are_as_expected()
    {
        $banner_template = new BannerTemplate([
            'banner_id' => 1,
            'template_id' => 2,
            'enabled' => true,
        ]);

        $this->assertIsBool($banner_template->enabled);
    }
}
