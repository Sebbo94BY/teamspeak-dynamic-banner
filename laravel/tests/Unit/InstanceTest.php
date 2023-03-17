<?php

namespace Tests\Unit;

use App\Models\Instance;
use Tests\TestCase;

class InstanceTest extends TestCase
{
    public function test_model_can_be_created()
    {
        $instance = new Instance([
            'virtualserver_name' => 'My TeamSpeak Server',
            'host' => '127.0.0.1',
            'voice_port' => 9987,
            'serverquery_port' => 10022,
            'is_ssh' => true,
            'serverquery_username' => 'serveradmin',
            'serverquery_password' => 'mySecretPassword',
            'client_nickname' => 'Dynamic Banner',
            'default_channel_id' => 1337,
            'autostart_enabled' => true,
        ]);

        $this->assertEquals('My TeamSpeak Server', $instance->virtualserver_name);
        $this->assertEquals('127.0.0.1', $instance->host);
        $this->assertEquals(9987, $instance->voice_port);
        $this->assertEquals(10022, $instance->serverquery_port);
        $this->assertEquals(true, $instance->is_ssh);
        $this->assertEquals('serveradmin', $instance->serverquery_username);
        $this->assertEquals('Dynamic Banner', $instance->client_nickname);
        $this->assertEquals(1337, $instance->default_channel_id);
        $this->assertEquals(true, $instance->autostart_enabled);
    }

    public function test_casts_are_as_expected()
    {
        $instance = new Instance([
            'virtualserver_name' => 'My TeamSpeak Server',
            'host' => '127.0.0.1',
            'voice_port' => 9987,
            'serverquery_port' => 10022,
            'is_ssh' => true,
            'serverquery_username' => 'serveradmin',
            'serverquery_password' => 'mySecretPassword',
            'client_nickname' => 'Dynamic Banner',
            'default_channel_id' => 1337,
            'autostart_enabled' => true,
        ]);

        $this->assertIsBool($instance->is_ssh);
        $this->assertIsBool($instance->autostart_enabled);
    }
}
