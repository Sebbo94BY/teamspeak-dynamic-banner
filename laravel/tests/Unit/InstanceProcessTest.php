<?php

namespace Tests\Unit;

use App\Models\InstanceProcess;
use Tests\TestCase;

class InstanceProcessTest extends TestCase
{
    public function test_model_can_be_created()
    {
        $instance_process = new InstanceProcess([
            'instance_id' => 1,
            'command' => 'php artisan instance:start-teamspeak-bot 1 --background',
            'process_id' => 1337,
        ]);

        $this->assertEquals(1, $instance_process->instance_id);
        $this->assertEquals('php artisan instance:start-teamspeak-bot 1 --background', $instance_process->command);
        $this->assertEquals(1337, $instance_process->process_id);
    }
}
