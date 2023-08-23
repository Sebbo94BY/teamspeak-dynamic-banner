<?php

namespace Tests\Unit\Models;

use App\Models\Instance;
use App\Models\InstanceProcess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstanceProcessTest extends TestCase
{
    use RefreshDatabase;

    protected Instance $instance;

    protected InstanceProcess $instance_process;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->instance = Instance::factory()->create();
        $this->instance_process = InstanceProcess::factory()->for($this->instance)->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->instance_process);
    }

    /**
     * Test that the model has one linked instance.
     */
    public function test_model_has_one_linked_instance(): void
    {
        $this->assertInstanceOf(Instance::class, $this->instance_process->instance);
    }
}
