<?php

namespace Tests\Unit;

use App\Models\Instance;
use App\Models\InstanceProcess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_can_be_created()
    {
        $instance = Instance::factory()->create();
        $this->assertModelExists($instance);

        $instance_proces = InstanceProcess::factory()->for($instance)->create();
        $this->assertModelExists($instance_proces);
    }

    public function test_model_casts()
    {
        $instance = Instance::factory()->create();

        $this->assertIsBool($instance->is_ssh);
        $this->assertIsBool($instance->autostart_enabled);
    }

    public function test_model_relationships()
    {
        $instance = Instance::factory()->create();
        InstanceProcess::factory()->for($instance)->create();

        $this->assertInstanceOf(InstanceProcess::class, $instance->process);
    }
}
