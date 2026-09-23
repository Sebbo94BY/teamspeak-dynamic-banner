<?php

return [

    'title' => 'Dashboard',
    'system_status_button' => 'Systemstatus',
    'background_tasks_overview' => 'Übersicht Hintergrundaufgaben',

    /**
     * Card: Instances
     */
    'instances_count_title' => '{1} :instances_count Instanz|{2,*] :instances_count Instanzen',
    'instances_count_text' => 'Eine Instanz kann als Datenquelle für die Banner angesehen werden.',
    'instances_button' => 'Instanzen anzeigen',
    'running_instances' => '{0} Keine laufenden Instanzen|{1} :running von :total Instanz läuft|[2,*] :running von :total Instanzen laufen',

    /**
     * Card: Templates
     */
    'templates_count_title' => '{1} :templates_count Vorlage|{2,*] :templates_count Vorlagen',
    'templates_count_text' => 'Eine Vorlage definiert das Design Ihres Banners.',
    'templates_button' => 'Vorlagen anzeigen',
    'static_templates' => '{0} keine statischen Bilder|{1} :count statisches Bild|[2,*] :count statische Bilder',
    'animated_templates' => '{0} keine animierten GIFs|{1} :count animiertes GIF|[2,*] :count animierte GIFs',

    /**
     * Card: Banners
     */
    'banners_count_title' => '{1} :banners_count Banner|{2,*] :banners_count Banners',
    'banners_count_text' => 'Ein Banner verwendet eine bestimmte Instanz und kann eine beliebige Anzahl von Vorlagen verwenden, um dynamische Bilder zu erzeugen.',
    'banners_button' => 'Banner anzeigen',

    'attention_title' => 'Benötigt Aufmerksamkeit',
    'attention_all_clear' => 'Alles in Ordnung',
    'stopped_instances' => '{1} :count gestoppte Instanz|[2,*] :count gestoppte Instanzen',
    'banners_without_templates' => '{1} :count Banner hat keine Vorlage|[2,*] :count Banner haben keine Vorlage',
    'banners_without_active_templates' => '{1} :count Banner hat keine aktive Vorlage|[2,*] :count Banner haben keine aktive Vorlage',
    'unused_templates' => '{1} :count Vorlage ist nicht zugeordnet|[2,*] :count Vorlagen sind nicht zugeordnet',
    'review_button' => 'Prüfen',

    'health_queue_unavailable' => 'Für diesen Queue-Treiber sind keine Queue-Metriken verfügbar.',
    'health_waiting_jobs' => 'Wartende Jobs',
    'health_processing_jobs' => 'Jobs in Verarbeitung',
    'health_stale_jobs' => 'Hängende Jobs',
    'health_failed_jobs' => 'Fehlgeschlagene Jobs',
    'health_hint' => 'Die vollständige Diagnose finden Sie im Systemstatus.',

    'recent_changes_title' => 'Letzte Änderungen',
    'recent_changes_empty' => 'Bisher keine Konfigurationsänderungen.',
    'change_type_instance' => 'Instanz',
    'change_type_template' => 'Vorlage',
    'change_type_banner' => 'Banner',
    'change_by' => 'von :name',
    'change_by_unknown' => 'Unbekannt',

    'banner_activity_title' => 'Letzte Banner-Renderings',
    'banner_activity_intro' => 'Wird aktualisiert, wenn eine Banner-URL erfolgreich gerendert und ausgeliefert wurde.',
    'banner_activity_empty' => 'Bisher keine Banner eingerichtet.',
    'banner_last_rendered' => 'Zuletzt gerendert',
    'banner_never_rendered' => 'Noch nicht gerendert',

    'quick_actions_title' => 'Schnellaktionen',
    'add_instance' => 'Instanz hinzufügen',
    'add_template' => 'Vorlage hinzufügen',
    'add_banner' => 'Banner hinzufügen',

];
