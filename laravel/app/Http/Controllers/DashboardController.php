<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\SystemStatusController as SystemStatusHelper;
use App\Models\Banner;
use App\Models\BannerTemplate;
use App\Models\Instance;
use App\Models\Template;
use App\Support\QueueMetrics;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function dashboard(): View
    {
        $queue_connection = config('queue.default');
        $queue_name = config('queue.connections.'.$queue_connection.'.queue', 'default');
        $queue_metrics = app(QueueMetrics::class)->current($queue_name);
        $failed_jobs_count = DB::table('failed_jobs')->count();
        $template_file_types = Template::query()->pluck('filename')->map(
            fn (string $filename) => strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
        );
        $system_status_severity = auth()->user()?->can('view system status')
            ? Cache::remember('dashboard-system-status-severity', now()->addMinute(), fn () => app(SystemStatusHelper::class)->overall_severity()->value)
            : null;

        return view('dashboard')->with([
            'instances_count' => Instance::count(),
            'running_instances_count' => Instance::has('process')->count(),
            'stopped_instances' => Instance::doesntHave('process')->orderBy('virtualserver_name')->get(),
            'templates_count' => Template::count(),
            'animated_templates_count' => $template_file_types->filter(fn (string $extension) => $extension === 'gif')->count(),
            'static_templates_count' => $template_file_types->filter(fn (string $extension) => $extension !== 'gif')->count(),
            'unused_templates' => Template::doesntHave('banner_templates')->orderBy('alias')->get(),
            'banners_count' => Banner::count(),
            'banners_without_templates' => Banner::doesntHave('templates')->orderBy('name')->get(),
            'banners_without_active_templates' => Banner::has('templates')->whereDoesntHave('templates', fn ($query) => $query->where('enabled', true))
                ->orderBy('name')
                ->get(),
            'recent_changes' => $this->recent_changes(),
            'recent_banner_renders' => Banner::withMax('templates', 'last_rendered_at')
                ->withCount('templates')
                ->orderByDesc('templates_max_last_rendered_at')
                ->orderBy('name')
                ->take(5)
                ->get(),
            'queue_metrics' => $queue_metrics,
            'failed_jobs_count' => $failed_jobs_count,
            'system_status_severity' => $system_status_severity,
        ]);
    }

    /**
     * Return the most recently changed configuration records in one timeline.
     */
    private function recent_changes(): Collection
    {
        return Instance::with('updatedBy')->latest('updated_at')->take(5)->get()->map(fn (Instance $instance) => (object) [
            'type' => 'instance',
            'name' => $instance->virtualserver_name,
            'changed_at' => $instance->updated_at,
            'changed_by' => $instance->updatedBy?->name,
        ])->concat(
            Template::with('updatedBy')->latest('updated_at')->take(5)->get()->map(fn (Template $template) => (object) [
                'type' => 'template',
                'name' => $template->alias,
                'changed_at' => $template->updated_at,
                'changed_by' => $template->updatedBy?->name,
            ]),
        )->concat(
            Banner::with('updatedBy')->latest('updated_at')->take(5)->get()->map(fn (Banner $banner) => (object) [
                'type' => 'banner',
                'name' => $banner->name,
                'changed_at' => $banner->updated_at,
                'changed_by' => $banner->updatedBy?->name,
            ]),
        )->concat(
            BannerTemplate::with(['banner', 'updatedBy'])->latest('updated_at')->take(10)->get()->map(fn (BannerTemplate $bannerTemplate) => (object) [
                'type' => 'banner',
                'name' => $bannerTemplate->banner->name,
                'changed_at' => $bannerTemplate->updated_at,
                'changed_by' => $bannerTemplate->updatedBy?->name,
            ]),
        )->sortByDesc('changed_at')->take(6)->values();
    }
}
