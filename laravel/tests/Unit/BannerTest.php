<?php

namespace Tests\Unit;

use App\Models\Banner;
use Tests\TestCase;

class BannerTest extends TestCase
{
    public function test_model_can_be_created()
    {
        $banner = new Banner([
            'name' => 'My Banner',
            'instance_id' => 1,
            'random_rotation' => false,
        ]);

        $this->assertEquals('My Banner', $banner->name);
        $this->assertEquals(1, $banner->instance_id);
        $this->assertEquals(false, $banner->random_rotation);
    }

    public function test_casts_are_as_expected()
    {
        $banner = new Banner([
            'name' => 'My Banner',
            'instance_id' => 1,
            'random_rotation' => false,
        ]);

        $this->assertIsBool($banner->random_rotation);
    }
}
