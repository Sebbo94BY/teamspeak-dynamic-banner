<?php

namespace App\Providers;

use App\Support\QueueWorkerHeartbeat;
use App\Support\QueueWorkerThroughput;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\Looping;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::looping(function (Looping $event) {
            app(QueueWorkerHeartbeat::class)->report($event->queue);
        });

        Queue::before(function (JobProcessing $event) {
            app(QueueWorkerThroughput::class)->started($event->job);
        });

        Queue::after(function (JobProcessed $event) {
            app(QueueWorkerThroughput::class)->completed($event->job);
        });
    }
}
