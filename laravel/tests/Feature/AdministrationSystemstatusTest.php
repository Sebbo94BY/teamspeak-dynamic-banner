<?php

namespace Tests\Feature;

use App\Models\Localization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdministrationSystemstatusTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the DatabaseSeeder
        $this->seed();

        $this->user = User::factory()->for(Localization::factory()->create())->create();
        $this->user->syncRoles('System Status Viewer');
    }

    /**
     * Test, that the user gets redirected to the login, when he is unauthenticated.
     */
    public function test_user_gets_redirected_to_login_when_unauthenticated(): void
    {
        $response = $this->get(route('administration.systemstatus'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /**
     * Test, that the user can access the page, when he is authenticated.
     */
    public function test_page_gets_displayed_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('administration.systemstatus'));
        $response->assertStatus(200);
        $response->assertViewIs('administration.systemstatus');
    }

    /**
     * Test, that the systemstatus page can be displayed.
     */
    public function test_systemstatus_view_gets_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(route('administration.systemstatus'));
        $response->assertStatus(200);
        $response->assertViewIs('administration.systemstatus');
    }

    /**
     * The queue view must explain that its size is not a worker recommendation.
     */
    public function test_queue_status_explains_the_meaning_of_the_queue_size(): void
    {
        $response = $this->actingAs($this->user)->get(route('administration.systemstatus'));

        $response->assertSee(__('views/inc/system/systemstatus.accordion_section_queue_health_size'));
        $response->assertSee(__('views/inc/system/systemstatus.accordion_section_queue_health_ready_jobs'));
        $response->assertSee(__('views/inc/system/systemstatus.accordion_section_queue_health_size_required_value'));
        $response->assertSee(__('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job_empty'));
    }

    /**
     * A task that has waited over five minutes needs a concrete next step.
     */
    public function test_queue_status_warns_about_an_old_waiting_task(): void
    {
        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->subMinutes(6)->timestamp,
            'created_at' => now()->subMinutes(6)->timestamp,
        ]);

        $response = $this->actingAs($this->user)->get(route('administration.systemstatus'));

        $response->assertSee(trans_choice('views/inc/system/systemstatus.accordion_section_queue_health_age_minutes', 6, ['count' => 6]));
        $response->assertSee(__('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job_delayed_action'));
    }

    /**
     * Running tasks are shown separately from tasks still waiting for a worker.
     */
    public function test_queue_status_does_not_describe_a_running_task_as_waiting(): void
    {
        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 1,
            'reserved_at' => now()->timestamp,
            'available_at' => now()->subMinute()->timestamp,
            'created_at' => now()->subMinute()->timestamp,
        ]);

        $response = $this->actingAs($this->user)->get(route('administration.systemstatus'));

        $response->assertSee(__('views/inc/system/systemstatus.accordion_section_queue_health_size'));
        $response->assertSee(__('views/inc/system/systemstatus.accordion_section_queue_health_ready_jobs'));
        $response->assertSee(__('views/inc/system/systemstatus.accordion_section_queue_health_processing_jobs'));
        $response->assertSee(__('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job_empty'));
    }

    /**
     * Administrators can select the displayed queue history period.
     */
    public function test_queue_history_range_can_be_selected(): void
    {
        $response = $this->actingAs($this->user)->get(route('administration.systemstatus', ['queue_history_range' => '30d']));

        $response->assertViewHas('queue_history_range', '30d');
    }

    /**
     * Unsupported queue history periods fall back to the default.
     */
    public function test_invalid_queue_history_range_falls_back_to_thirty_minutes(): void
    {
        $response = $this->actingAs($this->user)->get(route('administration.systemstatus', ['queue_history_range' => 'invalid']));

        $response->assertViewHas('queue_history_range', '30m');
    }
}
