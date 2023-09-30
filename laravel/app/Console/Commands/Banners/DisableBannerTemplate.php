<?php

namespace App\Console\Commands\Banners;

use App\Models\BannerTemplate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DisableBannerTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:disable-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disables templates of all banners, when their `disable_at` is after the current time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $banner_templates_with_set_disable_at = BannerTemplate::whereNotNull('disable_at')->get(['id', 'disable_at', 'enabled']);

        $this->info('Checking '.count($banner_templates_with_set_disable_at).' banner templates...');

        foreach ($banner_templates_with_set_disable_at as $banner_template) {
            if ($banner_template->disable_at->gte(Carbon::now())) {
                $this->info("The configured date and time for the banner template with the ID $banner_template->id is still in the future. Skipping.");
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
