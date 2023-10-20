<?php

namespace App\Console\Commands\Banners;

use App\Models\BannerTemplate;
use App\Models\TwitchApi;
use Illuminate\Console\Command;

class TwitchBasedEnabling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:twitch-based-enabling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enables templates of all banners, when the configured Twitch streamer is online.';

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
            if ($banner_template->enabled) {
                $this->info("The banner template with the ID $banner_template->id is already enabled. Skipping.");
                continue;
            }

            if (! $banner_template->twitch_streamer->is_live) {
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
