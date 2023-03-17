<?php

namespace Tests\Unit;

use App\Models\BannerConfiguration;
use Tests\TestCase;

class BannerConfigurationTest extends TestCase
{
    public function test_model_can_be_created()
    {
        $banner_configuration = new BannerConfiguration([
            'banner_template_id' => 1,
            'x_coordinate' => 380,
            'y_coordinate' => 150,
            'text' => 'Hello %CLIENT_NICKNAME%!',
            'font_size' => 5,
            'font_color_in_hexadecimal' => '#000000',
        ]);

        $this->assertEquals(1, $banner_configuration->banner_template_id);
        $this->assertEquals(380, $banner_configuration->x_coordinate);
        $this->assertEquals(150, $banner_configuration->y_coordinate);
        $this->assertEquals('Hello %CLIENT_NICKNAME%!', $banner_configuration->text);
        $this->assertEquals(5, $banner_configuration->font_size);
        $this->assertEquals('#000000', $banner_configuration->font_color_in_hexadecimal);
    }
}
