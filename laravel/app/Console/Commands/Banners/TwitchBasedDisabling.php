<?php

namespace App\Console\Commands\Banners;

use App\Models\BannerTemplate;
use App\Models\TwitchApi;
use Illuminate\Console\Command;

class TwitchBasedDisabling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:twitch-based-disabling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disables templates of all banners, when the configured Twitch streamer is offline.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (is_null(TwitchApi::first())) {
            return $this->info('No twitch API has been configured yet. Skipping.');
        }

        $banner_templates_with_set_twitch_streamer = BannerTemplate::whereNotNull('twitch_streamer_id')->get(['id', 'twitch_streamer_id', 'enabled']);

        $this->info('Checking '.count($banner_templates_with_set_twitch_streamer).' banner templates...');

        foreach ($banner_templates_with_set_twitch_streamer as $banner_template) {
            if (! $banner_template->enabled) {
                $this->info("The banner template with the ID $banner_template->id is already disabled. Skipping.");
                continue;
            }

            if ($banner_template->twitch_streamer->is_live) {
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
