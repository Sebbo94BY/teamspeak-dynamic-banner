<?php

namespace App\Console\Commands\Banners;

use App\Models\BannerTemplate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TimeBasedEnabling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:enable-time-based';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enables templates of all banners, when their `time_based_enable_at` is after the current time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $banner_templates_with_set_time_based_enable_at = BannerTemplate::whereNotNull('time_based_enable_at')->get(['id', 'time_based_enable_at', 'time_based_disable_at', 'enabled']);

        $this->info('Checking '.count($banner_templates_with_set_time_based_enable_at).' banner templates...');

        foreach ($banner_templates_with_set_time_based_enable_at as $banner_template) {
            if ($banner_template->enabled) {
                $this->info("The banner template with the ID $banner_template->id is already enabled. Skipping.");
                continue;
            }

            $carbon_time_based_enable_at = Carbon::parse($banner_template->time_based_enable_at);

            $should_be_enabled = false;
            if (is_null($banner_template->time_based_disable_at) and Carbon::now()->gt($carbon_time_based_enable_at)) {
                // Should be enabled every day
                // Example: 18:00
                $should_be_enabled = true;
            }

            if (! is_null($banner_template->time_based_disable_at) and ($carbon_time_based_enable_at > Carbon::parse($banner_template->time_based_disable_at))) {
                // Should be enabled on the same day
                // Example: 16:00 - 20:00
                if (Carbon::now()->lt($carbon_time_based_enable_at) and Carbon::now()->lt($banner_template->time_based_disable_at)) {
                    $should_be_enabled = true;
                } elseif (Carbon::now()->gt($carbon_time_based_enable_at) and Carbon::now()->gt($banner_template->time_based_disable_at)) {
                    $should_be_enabled = true;
                }
            } elseif (! is_null($banner_template->time_based_disable_at)) {
                // Should be enabled on the next day
                // Example: 22:00 - 01:00
                if (Carbon::now()->gt($carbon_time_based_enable_at) and Carbon::now()->lt($banner_template->time_based_disable_at)) {
                    $should_be_enabled = true;
                }
            }

            if (! $should_be_enabled) {
                $this->info("The banner template with the ID $banner_template->id should NOT be enabled yet. Skipping.");
                continue;
            }

            $banner_template->enabled = true;

            if (! $banner_template->save()) {
                $this->error("Failed to enable the banner template with the ID $banner_template->id. See ".route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id]).' for details.');
            }

            $this->warn("Successfully enabled the banner template with the ID $banner_template->id. See ".route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id]).' for details.');
        }
    }
}
