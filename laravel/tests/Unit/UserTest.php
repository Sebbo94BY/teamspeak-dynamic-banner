<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_can_be_created()
    {
        $user = User::factory()->create();

        $this->assertModelExists($user);
    }
}
