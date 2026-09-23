<?php

namespace App\Jobs;

use App\Support\ThrottledErrorLogger;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrackMatomoPageView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Do not retry failed tracking requests when Matomo is unavailable. */
    public int $tries = 1;

    public function __construct(
        private readonly string $document_title,
        private readonly string $url,
        private readonly ?string $referrer,
        private readonly ?string $ip_address,
        private readonly ?string $user_agent,
        private readonly ?string $accept_language,
    ) {
    }

    /** Send the page view to Matomo outside the banner HTTP request. */
    public function handle(): void
    {
        if (! config('matomo.enabled')) {
            return;
        }

        try {
            $matomo = new \MatomoTracker(config('matomo.site_id'), config('matomo.base_url'));
            $matomo->disableCookieSupport();
            $matomo->setRequestConnectTimeout(config('matomo.connect_timeout'));
            $matomo->setUrl($this->url);
            $matomo->setUrlReferrer($this->referrer);

            if (! is_null($this->ip_address)) {
                $matomo->setIp($this->ip_address);
            }

            if (! is_null($this->user_agent)) {
                $matomo->setUserAgent($this->user_agent);
            }

            if (! is_null($this->accept_language)) {
                $matomo->setBrowserLanguage($this->accept_language);
            }

            $matomo->doTrackPageView($this->document_title);
        } catch (Exception $matomo_exception) {
            $message = 'Matomo tracking failed. Received the following error: '.preg_replace('/\s\s+/', ' ', $matomo_exception->getMessage());
            (new ThrottledErrorLogger)->log($message, config('matomo.error_log_cooldown'));
        }
    }
}
