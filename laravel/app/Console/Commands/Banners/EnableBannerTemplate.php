<?php

namespace App\Console\Commands\Banners;

use App\Models\BannerTemplate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EnableBannerTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:enable-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enables templates of all banners, when their `enable_at` is after the current time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $banner_templates_with_set_enable_at = BannerTemplate::whereNotNull('enable_at')->get(['id', 'enable_at', 'enabled']);

        $this->info('Checking '.count($banner_templates_with_set_enable_at).' banner templates...');

        foreach ($banner_templates_with_set_enable_at as $banner_template) {
            if ($banner_template->enable_at->gte(Carbon::now())) {
                $this->info("The configured date and time for the banner template with the ID $banner_template->id is still in the future. Skipping.");
                continue;
            }

            // Unconfigure the `enable_at` afterwards as otherwise a configured `disable_at` will
            // always cause the banner to get enabled and disabled in a loop.
            $banner_template->enable_at = null;
            $banner_template->enabled = true;

            if (! $banner_template->save()) {
                $this->error("Failed to enable the banner template with the ID $banner_template->id. See ".route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id]).' for details.');
            }

            $this->warn("Successfully enabled the banner template with the ID $banner_template->id. See ".route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id]).' for details.');
        }
    }
}
