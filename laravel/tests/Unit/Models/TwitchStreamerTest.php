<?php

namespace Tests\Unit\Models;

use App\Models\TwitchStreamer;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class TwitchStreamerTest extends TestCase
{
    use RefreshDatabase;

    protected TwitchStreamer $twitch_streamer;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->twitch_streamer = TwitchStreamer::factory()->create();
    }

    /**
     * Test that the model can be created.
     */
    public function test_model_can_be_created(): void
    {
        $this->assertModelExists($this->twitch_streamer);
    }

    /**
     * Test that the model properly casts specific attributes.
     */
    public function test_model_properly_casts_specific_attributes(): void
    {
        $this->assertIsBool($this->twitch_streamer->is_live);
        $this->assertInstanceOf(Carbon::class, $this->twitch_streamer->started_at);
    }
}
