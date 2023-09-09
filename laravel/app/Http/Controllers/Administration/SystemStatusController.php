<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\SystemStatusController as HelpersSystemStatusController;
use Illuminate\View\View;

class SystemStatusController extends Controller
{
    /**
     * Display system status information.
     */
    public function system_status(string $headline = 'Systemstatus', bool $installer = false): View
    {
        $system_status_helper = new HelpersSystemStatusController();
        $system_status = collect(json_decode($system_status_helper->system_status_json()));
        $php_status = collect($system_status['PHP']);
        $php_extensions = collect($php_status['EXTENSIONS']);
        $php_ini_settings = collect($php_status['INI_SETTINGS']);

        $db_status = collect($system_status['DATABASE']);
        $db_status_connection =collect($db_status['CONNECTION']);
        $db_status_settings = collect($db_status['SETTINGS']);

        $permission_status = collect($system_status['PERMISSIONS']);
        $permission_status_dir = collect($permission_status['DIRECTORIES']);

        $redis_staus = collect($system_status['REDIS']);
        $redis_staus_connection = collect($redis_staus['CONNECTION']);

        $versions_status = collect($system_status['VERSIONS']);
        $versions_status_software = collect($versions_status['SOFTWARE']);

        $various_status = collect($system_status['VARIOUS']);
        $various_status_information = collect($various_status['INFORMATION']);

        return view('administration.systemstatus', [
            'installer'=>$installer,
            'headline'=>$headline,
            'php_status'=>$php_status,
            'php_status_extension'=>$php_extensions,
            'php_status_ini_settings'=>$php_ini_settings,
            'php_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $php_status),
            'php_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $php_status),
            'db_status_connection'=>$db_status_connection,
            'db_status_Settings'=>$db_status_settings,
            'db_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $db_status),
            'db_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $db_status),
            'permission_status_dir' => $permission_status_dir,
            'permission_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $permission_status),
            'permission_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $permission_status),
            'redis_status_connection'=>$redis_staus_connection,
            'redis_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $redis_staus),
            'redis_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $redis_staus),
            'version_status_software'=>$versions_status_software,
            'version_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $versions_status),
            'version_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $versions_status),
            'various_status_information'=>$various_status_information,
            'various_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $various_status),
            'various_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $various_status),
            'system_status_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $system_status),
            'system_status_danger_count' => preg_match_all("/\"severity\"\:\"danger\"/", $system_status),
        ]);
    }
}
