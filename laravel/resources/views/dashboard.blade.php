@extends('layout')

@section('site_title')
    Dashboard
@endsection

@section('content')
    <div class="container mt-3">
        @include('inc.standard-alerts')
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="fw-bold fs-3 mb-0">{{ __('views/dashboard.title') }}</h1>
            @can('view system status')
                <a href="{{ route('administration.systemstatus') }}" class="btn btn-{{ $system_status_severity }} btn-sm">
                    <i class="fa-solid fa-heart-pulse me-1"></i>{{ __('views/dashboard.system_status_button') }}
                </a>
            @endcan
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-3">
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ trans_choice('views/dashboard.instances_count_title', $instances_count, ['instances_count' => $instances_count]) }}</h5>
                        <p class="card-text mb-1">{{ __("views/dashboard.instances_count_text") }}</p>
                        <span class="small {{ $running_instances_count === $instances_count ? 'text-success' : 'text-warning' }}">{{ trans_choice('views/dashboard.running_instances', $running_instances_count, ['running' => $running_instances_count, 'total' => $instances_count]) }}</span>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view instances')
                        <a href="{{ route('instances') }}" class="btn btn-primary">{{ __("views/dashboard.instances_button") }}</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ trans_choice('views/dashboard.templates_count_title', $templates_count, ['templates_count' => $templates_count]) }}</h5>
                        <p class="card-text mb-1">{{ __("views/dashboard.templates_count_text") }}</p>
                        <span class="small text-muted">{{ trans_choice('views/dashboard.static_templates', $static_templates_count, ['count' => $static_templates_count]) }} · {{ trans_choice('views/dashboard.animated_templates', $animated_templates_count, ['count' => $animated_templates_count]) }}</span>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view templates')
                        <a href="{{ route('templates') }}" class="btn btn-primary">{{ __("views/dashboard.templates_button") }}</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ trans_choice('views/dashboard.banners_count_title', $banners_count, ['banners_count' => $banners_count]) }}</h5>
                        <p class="card-text">{{ __("views/dashboard.banners_count_text") }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view banners')
                        <a href="{{ route('banners') }}" class="btn btn-primary">{{ __("views/dashboard.banners_button") }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h2 class="card-title fs-5">{{ __('views/dashboard.quick_actions_title') }}</h2>
                <div class="d-flex flex-wrap gap-2">
                    @can('add instances')<a href="{{ route('instances', ['create' => 1]) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-plus me-1"></i>{{ __('views/dashboard.add_instance') }}</a>@endcan
                    @can('add templates')<a href="{{ route('templates', ['create' => 1]) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-plus me-1"></i>{{ __('views/dashboard.add_template') }}</a>@endcan
                    @can('add banners')<a href="{{ route('banners', ['create' => 1]) }}" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-plus me-1"></i>{{ __('views/dashboard.add_banner') }}</a>@endcan
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="card-title fs-5 mb-0">{{ __('views/dashboard.attention_title') }}</h2>
                            @if($stopped_instances->isEmpty() && $banners_without_templates->isEmpty() && $banners_without_active_templates->isEmpty() && $unused_templates->isEmpty())
                                <span class="badge text-bg-success">{{ __('views/dashboard.attention_all_clear') }}</span>
                            @endif
                        </div>
                        @if($stopped_instances->isNotEmpty())
                            <div class="d-flex justify-content-between align-items-center py-2 border-top">
                                <span><i class="fa-solid fa-circle-stop text-danger me-2"></i>{{ trans_choice('views/dashboard.stopped_instances', $stopped_instances->count(), ['count' => $stopped_instances->count()]) }}</span>
                                @can('view instances')<a href="{{ route('instances', ['attention' => 'stopped']) }}" class="btn btn-sm btn-outline-primary">{{ __('views/dashboard.review_button') }}</a>@endcan
                            </div>
                        @endif
                        @if($banners_without_templates->isNotEmpty())
                            <div class="d-flex justify-content-between align-items-center py-2 border-top">
                                <span><i class="fa-solid fa-image text-warning me-2"></i>{{ trans_choice('views/dashboard.banners_without_templates', $banners_without_templates->count(), ['count' => $banners_without_templates->count()]) }}</span>
                                @can('view banners')<a href="{{ route('banners', ['attention' => 'without-templates']) }}" class="btn btn-sm btn-outline-primary">{{ __('views/dashboard.review_button') }}</a>@endcan
                            </div>
                        @endif
                        @if($banners_without_active_templates->isNotEmpty())
                            <div class="d-flex justify-content-between align-items-center py-2 border-top">
                                <span><i class="fa-solid fa-pause text-warning me-2"></i>{{ trans_choice('views/dashboard.banners_without_active_templates', $banners_without_active_templates->count(), ['count' => $banners_without_active_templates->count()]) }}</span>
                                @can('view banners')<a href="{{ route('banners', ['attention' => 'without-active-templates']) }}" class="btn btn-sm btn-outline-primary">{{ __('views/dashboard.review_button') }}</a>@endcan
                            </div>
                        @endif
                        @if($unused_templates->isNotEmpty())
                            <div class="d-flex justify-content-between align-items-center py-2 border-top">
                                <span><i class="fa-solid fa-file-image text-secondary me-2"></i>{{ trans_choice('views/dashboard.unused_templates', $unused_templates->count(), ['count' => $unused_templates->count()]) }}</span>
                                @can('view templates')<a href="{{ route('templates', ['attention' => 'unused']) }}" class="btn btn-sm btn-outline-primary">{{ __('views/dashboard.review_button') }}</a>@endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title fs-5">{{ __('views/dashboard.background_tasks_overview') }}</h2>
                        @if(is_null($queue_metrics))
                            <p class="mb-2 text-muted">{{ __('views/dashboard.health_queue_unavailable') }}</p>
                        @else
                            <div class="d-flex justify-content-between"><span>{{ __('views/dashboard.health_waiting_jobs') }}</span><strong>{{ $queue_metrics['ready'] }}</strong></div>
                            <div class="d-flex justify-content-between"><span>{{ __('views/dashboard.health_processing_jobs') }}</span><strong>{{ $queue_metrics['processing'] }}</strong></div>
                            <div class="d-flex justify-content-between"><span>{{ __('views/dashboard.health_stale_jobs') }}</span><strong class="{{ $queue_metrics['stale'] > 0 ? 'text-danger' : '' }}">{{ $queue_metrics['stale'] }}</strong></div>
                        @endif
                        <div class="d-flex justify-content-between mt-2 pt-2 border-top"><span>{{ __('views/dashboard.health_failed_jobs') }}</span><strong class="{{ $failed_jobs_count > 0 ? 'text-danger' : '' }}">{{ $failed_jobs_count }}</strong></div>
                        <p class="small text-muted mt-3 mb-0">{{ __('views/dashboard.health_hint') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title fs-5">{{ __('views/dashboard.recent_changes_title') }}</h2>
                        @forelse($recent_changes as $change)
                            <div class="d-flex justify-content-between align-items-center py-2 border-top">
                                <span><span class="badge text-bg-light border text-dark me-2">{{ __('views/dashboard.change_type_'.$change->type) }}</span>{{ $change->name }}</span>
                                <span class="small text-muted text-end"><span>{{ __('views/dashboard.change_by', ['name' => $change->changed_by ?? __('views/dashboard.change_by_unknown')]) }}</span><br><time datetime="{{ $change->changed_at->toIso8601String() }}">{{ $change->changed_at->setTimezone(Request::header('X-Timezone'))->diffForHumans() }}</time></span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('views/dashboard.recent_changes_empty') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title fs-5 mb-1">{{ __('views/dashboard.banner_activity_title') }}</h2>
                        <p class="small text-muted mb-2">{{ __('views/dashboard.banner_activity_intro') }}</p>
                        @forelse($recent_banner_renders as $banner)
                            <div class="d-flex justify-content-between align-items-center py-2 border-top">
                                @can('edit banners')
                                    <a href="{{ route('banner.templates', ['banner_id' => $banner->id]) }}">{{ $banner->name }}</a>
                                @else
                                    <span>{{ $banner->name }}</span>
                                @endcan
                                @if($banner->templates_max_last_rendered_at)
                                    @php($lastRenderedAt = Carbon\Carbon::parse($banner->templates_max_last_rendered_at))
                                    <time class="small text-muted text-end" datetime="{{ $lastRenderedAt->toIso8601String() }}"><span class="d-block">{{ __('views/dashboard.banner_last_rendered') }}</span>{{ $lastRenderedAt->setTimezone(Request::header('X-Timezone'))->diffForHumans() }}</time>
                                @else
                                    <span class="small text-muted">{{ __('views/dashboard.banner_never_rendered') }}</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('views/dashboard.banner_activity_empty') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
