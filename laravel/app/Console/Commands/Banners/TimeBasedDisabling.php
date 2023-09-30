<?php

namespace App\Console\Commands\Banners;

use App\Models\BannerTemplate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TimeBasedDisabling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:disable-time-based';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disables templates of all banners, when their `time_based_disable_at` is after the current time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $banner_templates_with_set_time_based_disable_at = BannerTemplate::whereNotNull('time_based_disable_at')->get(['id', 'time_based_enable_at', 'time_based_disable_at', 'enabled']);

        $this->info('Checking '.count($banner_templates_with_set_time_based_disable_at).' banner templates...');

        foreach ($banner_templates_with_set_time_based_disable_at as $banner_template) {
            if (! $banner_template->enabled) {
                $this->info("The banner template with the ID $banner_template->id is already disabled. Skipping.");
                continue;
            }

            $carbon_time_based_disable_at = Carbon::parse($banner_template->time_based_disable_at);

            $should_be_disabled = false;
            if ($carbon_time_based_disable_at > Carbon::parse($banner_template->time_based_enable_at)) {
                // Should be disabled on the same day
                // Example: 16:00 - 20:00
                if (Carbon::now()->lt($carbon_time_based_disable_at) and Carbon::now()->lt($banner_template->time_based_enable_at)) {
                    $should_be_disabled = true;
                } elseif (Carbon::now()->gt($carbon_time_based_disable_at) and Carbon::now()->gt($banner_template->time_based_enable_at)) {
                    $should_be_disabled = true;
                }
            } else {
                // Should be disabled on the next day
                // Example: 22:00 - 01:00
                if (Carbon::now()->gt($carbon_time_based_disable_at) and Carbon::now()->lt($banner_template->time_based_enable_at)) {
                    $should_be_disabled = true;
                }
            }

            if (! $should_be_disabled) {
                $this->info("The banner template with the ID $banner_template->id should NOT be disabled yet. Skipping.");
                continue;
            }

            $banner_template->enabled = false;

            if (! $banner_template->save()) {
                $this->error("Failed to disable the banner template with the ID $banner_template->id. See ".route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id]).' for details.');
            }

            $this->warn("Successfully disabled the banner template with the ID $banner_template->id. See ".route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id]).' for details.');
        }
    }
}
