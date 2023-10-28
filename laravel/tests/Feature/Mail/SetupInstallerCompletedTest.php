<?php

namespace Tests\Feature\Mail;

use App\Mail\SetupInstallerCompleted;
use App\Models\Localization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SetupInstallerCompletedTest extends TestCase
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
        $this->user->syncRoles('Super Admin');
    }

    /**
     * Test that the mailable content looks as expected
     */
    public function test_mailable_content_looks_as_expected(): void
    {
        $mailable = new SetupInstallerCompleted($this->user);
        $mailable->assertTo($this->user->email);
        $mailable->assertHasSubject('Installation Completed');
        $mailable->assertSeeInOrderInHtml(['Hello', 'Enjoy the application']);
        $mailable->assertSeeInOrderInText(['Hello', 'Enjoy the application']);
    }

    /**
     * Test that the mailable can be queued
     */
    public function test_mailable_can_be_queued(): void
    {
        Mail::fake();

        Mail::send(new SetupInstallerCompleted($this->user));

        Mail::assertQueued(SetupInstallerCompleted::class);
        Mail::assertQueuedCount(1);
    }
}
