<?php

namespace Tests\Feature\Mail;

use App\Mail\UserCreated;
use App\Models\Localization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserCreatedTest extends TestCase
{
    use RefreshDatabase;

    protected Localization $localization;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->localization = Localization::factory()->create();

        $this->user = User::factory()->for($this->localization)->create();
        $this->user->syncRoles('Users Admin');
    }

    /**
     * Test that the mailable content looks as expected
     */
    public function test_mailable_content_looks_as_expected(): void
    {
        $new_user = User::factory()->create();
        $initial_password = Str::random(10);

        $mailable = new UserCreated($this->user, $new_user, $initial_password);
        $mailable->assertTo($new_user->email);
        $mailable->assertHasSubject('Invitation to '.config('app.name'));
        $mailable->assertSeeInOrderInHtml(['Hello', $new_user->name, $this->user->name, $initial_password, 'Thanks']);
        $mailable->assertSeeInOrderInText(['Hello', $new_user->name, $this->user->name, $initial_password, 'Thanks']);
    }

    /**
     * Test that the mailable can be queued
     */
    public function test_mailable_can_be_queued(): void
    {
        $new_user = User::factory()->create();
        $initial_password = Str::random(10);

        Mail::fake();

        Mail::send(new UserCreated($this->user, $new_user, $initial_password));

        Mail::assertQueued(UserCreated::class);
        Mail::assertQueuedCount(1);
    }
}
