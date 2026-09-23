<?php

return [

    'title' => 'Dashboard',
    'system_status_button' => 'System status',
    'background_tasks_overview' => 'Background tasks overview',

    /**
     * Card: Instances
     */
    'instances_count_title' => '{1} :instances_count Instance|{2,*] :instances_count Instances',
    'instances_count_text' => 'An instance can be seen as data source for the banners.',
    'instances_button' => 'View Instances',
    'running_instances' => '{0} No running instances|{1} :running of :total instance running|[2,*] :running of :total instances running',

    /**
     * Card: Templates
     */
    'templates_count_title' => '{1} :templates_count Template|{2,*] :templates_count Templates',
    'templates_count_text' => 'A template defines the design of your banner.',
    'templates_button' => 'View Templates',
    'static_templates' => '{0} no static images|{1} :count static image|[2,*] :count static images',
    'animated_templates' => '{0} no animated GIFs|{1} :count animated GIF|[2,*] :count animated GIFs',

    /**
     * Card: Banners
     */
    'banners_count_title' => '{1} :banners_count Banner|{2,*] :banners_count Banners',
    'banners_count_text' => 'A banner uses a specific instance and can use any number of templates to generate dynamic images.',
    'banners_button' => 'View Banners',

    'attention_title' => 'Needs attention',
    'attention_all_clear' => 'All clear',
    'stopped_instances' => '{1} :count stopped instance|[2,*] :count stopped instances',
    'banners_without_templates' => '{1} :count banner has no template|[2,*] :count banners have no template',
    'banners_without_active_templates' => '{1} :count banner has no active template|[2,*] :count banners have no active template',
    'unused_templates' => '{1} :count template is not assigned|[2,*] :count templates are not assigned',
    'review_button' => 'Review',

    'health_queue_unavailable' => 'Queue metrics are unavailable for this queue driver.',
    'health_waiting_jobs' => 'Waiting jobs',
    'health_processing_jobs' => 'Processing jobs',
    'health_stale_jobs' => 'Stale jobs',
    'health_failed_jobs' => 'Failed jobs',
    'health_hint' => 'Open System status for the complete diagnostics.',

    'recent_changes_title' => 'Recent changes',
    'recent_changes_empty' => 'No configuration changes yet.',
    'change_type_instance' => 'Instance',
    'change_type_template' => 'Template',
    'change_type_banner' => 'Banner',
    'change_by' => 'by :name',
    'change_by_unknown' => 'Unknown',

    'banner_activity_title' => 'Latest banner renderings',
    'banner_activity_intro' => 'Updated when a banner URL is successfully rendered and delivered.',
    'banner_activity_empty' => 'No banners configured yet.',
    'banner_last_rendered' => 'Last rendered',
    'banner_never_rendered' => 'Not rendered yet',

    'quick_actions_title' => 'Quick actions',
    'add_instance' => 'Add instance',
    'add_template' => 'Add template',
    'add_banner' => 'Add banner',

];
