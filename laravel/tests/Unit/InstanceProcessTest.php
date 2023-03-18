<?php

namespace Tests\Unit;

use App\Models\Instance;
use App\Models\InstanceProcess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstanceProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_can_be_created()
    {
        $instance_process = InstanceProcess::factory()->for(
            Instance::factory()->create()
        )->create();

        $this->assertModelExists($instance_process);
    }
}
