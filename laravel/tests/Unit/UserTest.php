<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_model_can_be_created()
    {
        $user = new User([
            'name' => 'Max Mustermann',
            'email' => 'max.mustermann@example.com',
            'password' => Hash::make(Str::random(rand(8, 31))),
        ]);

        $this->assertEquals('Max Mustermann', $user->name);
        $this->assertEquals('max.mustermann@example.com', $user->email);
    }
}
