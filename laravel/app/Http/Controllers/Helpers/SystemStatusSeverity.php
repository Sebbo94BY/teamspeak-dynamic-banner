<?php

namespace App\Http\Controllers\Helpers;

/**
 * Possible system status severities.
 */
enum SystemStatusSeverity: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
}
