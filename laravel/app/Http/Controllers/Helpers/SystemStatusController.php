<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use ErrorException;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Predis\Connection\ConnectionException;

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

class SystemStatusController extends Controller
{
    /**
     * Checks PHP version.
     */
    protected function check_php_version(): array
    {
        $requirements = [];

        $requirements['VERSION_ID']['name'] = __('views/inc/system/systemstatus.accordion_section_php_version');
        $requirements['VERSION_ID']['current_value'] = PHP_VERSION_ID.' ('.PHP_VERSION.')';
        $requirements['VERSION_ID']['required_value'] = '>= 80100 (8.1.0)';
        $requirements['VERSION_ID']['severity'] = (PHP_VERSION_ID >= 80100) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks PHP extensions.
     */
    protected function check_php_extensions(): array
    {
        $requirements = [];

        /**
         * Laravel specific requirements
         */
        $requirements['CTYPE']['name'] = 'ctype';
        $requirements['CTYPE']['severity'] = (extension_loaded('ctype')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['CURL']['name'] = 'curl';
        $requirements['CURL']['severity'] = (extension_loaded('curl')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['DOM']['name'] = 'dom';
        $requirements['DOM']['severity'] = (extension_loaded('dom')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['FILEINFO']['name'] = 'fileinfo';
        $requirements['FILEINFO']['severity'] = (extension_loaded('fileinfo')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['FILTER']['name'] = 'filter';
        $requirements['FILTER']['severity'] = (extension_loaded('filter')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['HASH']['name'] = 'hash';
        $requirements['HASH']['severity'] = (extension_loaded('hash')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['MBSTRING']['name'] = 'mbstring';
        $requirements['MBSTRING']['severity'] = (extension_loaded('mbstring')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['OPENSSL']['name'] = 'openssl';
        $requirements['OPENSSL']['severity'] = (extension_loaded('openssl')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PCRE']['name'] = 'pcre';
        $requirements['PCRE']['severity'] = (extension_loaded('openssl')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PDO']['name'] = 'pdo';
        $requirements['PDO']['severity'] = (extension_loaded('pdo')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['SESSION']['name'] = 'session';
        $requirements['SESSION']['severity'] = (extension_loaded('session')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['TOKENIZER']['name'] = 'tokenizer';
        $requirements['TOKENIZER']['severity'] = (extension_loaded('tokenizer')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['XML']['name'] = 'xml';
        $requirements['XML']['severity'] = (extension_loaded('xml')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PDO_MYSQL']['name'] = 'pdo_mysql';
        $requirements['PDO_MYSQL']['severity'] = (extension_loaded('pdo_mysql')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        /**
         * Project specific requirements
         */
        $requirements['SSH2']['name'] = 'ssh2';
        $requirements['SSH2']['severity'] = (extension_loaded('ssh2')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $requirements['GD_PNG_SUPPORT']['name'] = 'gd (PNG Support)';
        $requirements['GD_PNG_SUPPORT']['severity'] = ((extension_loaded('gd')) ?? gd_info()['PNG Support']) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $requirements['GD_JPEG_SUPPORT']['name'] = 'gd (JPEG Support)';
        $requirements['GD_JPEG_SUPPORT']['severity'] = ((extension_loaded('gd')) ?? gd_info()['JPEG Support']) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        return $requirements;
    }

    /**
     * Checks PHP INI setings.
     */
    protected function check_php_ini_settings(): array
    {
        $disable_functions = ini_get('disable_functions');
        $requirements['DISABLE_FUNCTIONS']['name'] = 'PHP disable_functions';
        $requirements['DISABLE_FUNCTIONS']['current_value'] = (empty(trim($disable_functions))) ? __('views/inc/system/systemstatus.accordion_section_php_ini_disable_functions_current_value_empty_list') : $disable_functions;
        $requirements['DISABLE_FUNCTIONS']['required_value'] = __('views/inc/system/systemstatus.accordion_section_php_ini_disable_functions_required_value');
        $requirements['DISABLE_FUNCTIONS']['severity'] = (preg_match('/shell_exec/', $disable_functions)) ? SystemStatusSeverity::Warning : SystemStatusSeverity::Success;

        $max_execution_time = ini_get('max_execution_time');
        $requirements['MAX_EXECUTION_TIME']['name'] = 'PHP max_execution_time';
        $requirements['MAX_EXECUTION_TIME']['current_value'] = $max_execution_time;
        $requirements['MAX_EXECUTION_TIME']['required_value'] = '0, -1 OR >=30';
        $requirements['MAX_EXECUTION_TIME']['severity'] = (
            ($max_execution_time == 0) or
            ($max_execution_time == '-1') or
            ($max_execution_time >= 0)
        ) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $memory_limit = ini_get('memory_limit');
        $requirements['MEMORY_LIMIT']['name'] = 'PHP memory_limit';
        $requirements['MEMORY_LIMIT']['current_value'] = $memory_limit;
        $requirements['MEMORY_LIMIT']['required_value'] = '>= 128M';
        $requirements['MEMORY_LIMIT']['severity'] = (substr($memory_limit, 0, -1) >= 128) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $upload_max_filesize = ini_get('upload_max_filesize');
        $requirements['UPLOAD_MAX_FILESIZE']['name'] = 'PHP upload_max_filesize';
        $requirements['UPLOAD_MAX_FILESIZE']['current_value'] = $upload_max_filesize;
        $requirements['UPLOAD_MAX_FILESIZE']['required_value'] = '>= 5M';
        $requirements['UPLOAD_MAX_FILESIZE']['severity'] = (substr($upload_max_filesize, 0, -1) * 1024 * 1024 >= 5 * 1024 * 1024) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $post_max_size = ini_get('post_max_size');
        $requirements['POST_MAX_SIZE']['name'] = 'PHP post_max_size';
        $requirements['POST_MAX_SIZE']['current_value'] = $post_max_size;
        $requirements['POST_MAX_SIZE']['required_value'] = '>= upload_max_filesize';
        $requirements['POST_MAX_SIZE']['severity'] = (substr($post_max_size, 0, -1) * 1024 * 1024 >= substr($upload_max_filesize, 0, -1) * 1024 * 1024) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $date_timezone = ini_get('date.timezone');
        $requirements['DATE_TIMEZONE']['name'] = 'PHP date.timezone';
        $requirements['DATE_TIMEZONE']['current_value'] = $date_timezone;
        $requirements['DATE_TIMEZONE']['required_value'] = __('views/inc/system/systemstatus.accordion_section_php_ini_date_timezone_required_value');
        $requirements['DATE_TIMEZONE']['severity'] = (! empty(trim($date_timezone))) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        return $requirements;
    }

    /**
     * Checks database connection.
     */
    protected function check_database_connection(): array
    {
        $requirements = [];

        try {
            $db_name = DB::connection()->getDatabaseName();
        } catch (Exception $exception) {
            $db_name = $exception;
        }

        $requirements['TEST']['name'] = __('views/inc/system/systemstatus.accordion_section_database_connection');
        $requirements['TEST']['current_value'] = (is_string($db_name)) ? __('views/inc/system/systemstatus.accordion_section_database_connection_current_value_connected') : __('views/inc/system/systemstatus.accordion_section_database_connection_current_value_error', ['exception' => $db_name]);
        $requirements['TEST']['required_value'] = __('views/inc/system/systemstatus.accordion_section_database_connection_required_value');
        $requirements['TEST']['severity'] = (is_string($db_name)) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks database settings.
     */
    protected function check_database_settings(): array
    {
        $requirements = [];

        try {
            $db_name = DB::connection()->getDatabaseName();
        } catch (Exception $exception) {
            $db_name = $exception->getMessage();
        }

        $requirements['DB_NAME']['name'] = __('views/inc/system/systemstatus.accordion_section_database_name');
        $requirements['DB_NAME']['current_value'] = $db_name;
        $requirements['DB_NAME']['required_value'] = null;
        $requirements['DB_NAME']['severity'] = (is_string($db_name)) ? SystemStatusSeverity::Info : SystemStatusSeverity::Danger;

        $username = config('database.connections.'.Config::get('database.default').'.username');
        $requirements['DB_USER']['name'] = __('views/inc/system/systemstatus.accordion_section_database_user');
        $requirements['DB_USER']['current_value'] = $username;
        $requirements['DB_USER']['required_value'] = __('views/inc/system/systemstatus.accordion_section_database_user_required_value');
        $requirements['DB_USER']['severity'] = ($username != 'root') ? SystemStatusSeverity::Info : SystemStatusSeverity::Warning;

        $charset = config('database.connections.'.Config::get('database.default').'.charset');
        $requirements['CHARACTER_SET']['name'] = __('views/inc/system/systemstatus.accordion_section_database_character_set');
        $requirements['CHARACTER_SET']['current_value'] = $charset;
        $requirements['CHARACTER_SET']['required_value'] = __('views/inc/system/systemstatus.accordion_section_database_character_set_required_value');
        $requirements['CHARACTER_SET']['severity'] = (preg_match('/^utf8/', $charset)) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $collation = config('database.connections.'.Config::get('database.default').'.collation');
        $requirements['COLLATION']['name'] = __('views/inc/system/systemstatus.accordion_section_database_collation');
        $requirements['COLLATION']['current_value'] = $collation;
        $requirements['COLLATION']['required_value'] = __('views/inc/system/systemstatus.accordion_section_database_collation_required_value');
        $requirements['COLLATION']['severity'] = (preg_match('/^utf8/', $collation)) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        return $requirements;
    }

    /**
     * Checks directories.
     */
    protected function check_directories(): array
    {
        $requirements = [];

        $requirements['STORAGE_FRAMEWORK_DIR']['name'] = storage_path('framework');
        $requirements['STORAGE_FRAMEWORK_DIR']['severity'] = (is_writable(storage_path('framework'))) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['STORAGE_LOGS_DIR']['name'] = storage_path('logs');
        $requirements['STORAGE_LOGS_DIR']['severity'] = (is_writable(storage_path('logs'))) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PUBLIC_DIR']['name'] = public_path();
        $requirements['PUBLIC_DIR']['severity'] = (is_writable(public_path())) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks Queue health.
     */
    protected function check_queue_health(): array
    {
        $requirements = [];

        $queue_size = Queue::size();

        $requirements['SIZE']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_size');
        $requirements['SIZE']['current_value'] = $queue_size;

        $total_cached_clients = 0;
        try {
            foreach (Instance::all() as $instance) {
                $cached_clients = Redis::hkeys('instance_'.$instance->id.'_clientlist');
                $total_cached_clients = substr_count(implode(',', $cached_clients), '_NICKNAME');
            }
        } catch (ConnectionException) {
            // Do nothing; Simply catch and ignore this error
        }

        if ($total_cached_clients == 0) {
            $requirements['SIZE']['severity'] = ($queue_size < $max_expected_queue_size = 25) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;
        } else {
            $requirements['SIZE']['severity'] = ($queue_size < $max_expected_queue_size = $total_cached_clients * 1.5) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;
        }

        $requirements['SIZE']['required_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_size_required_value', ['max_expected_queue_size' => $max_expected_queue_size]);

        return $requirements;
    }

    /**
     * Checks Redis connection.
     */
    protected function check_redis_connection(): array
    {
        $requirements = [];

        $reachable = false;
        try {
            Redis::ping();
            $reachable = true;
        } catch (ConnectionException $connection_exception) {
            $redis_connection_exception = $connection_exception->getMessage();
        }

        $requirements['TEST']['name'] = __('views/inc/system/systemstatus.accordion_section_redis_connection');
        $requirements['TEST']['current_value'] = ($reachable) ? __('views/inc/system/systemstatus.accordion_section_redis_connection_current_value_connected') : __('views/inc/system/systemstatus.accordion_section_redis_connection_current_value_error', ['exception' => $redis_connection_exception]);
        $requirements['TEST']['required_value'] = __('views/inc/system/systemstatus.accordion_section_redis_connection_required_value');
        $requirements['TEST']['severity'] = ($reachable) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks FFMpeg version.
     */
    protected function check_ffmpeg_version(): array
    {
        $requirements = [];

        $command_output = (preg_match('/shell_exec/', ini_get('disable_functions'))) ? null : @shell_exec('ffmpeg -version | head -1');

        $requirements['VERSION']['name'] = __('views/inc/system/systemstatus.accordion_section_ffmpeg_version');
        $requirements['VERSION']['current_value'] = (is_null($command_output) or empty(trim($command_output))) ? __('views/inc/system/systemstatus.accordion_section_ffmpeg_version_current_value_error') : $command_output;
        $requirements['VERSION']['required_value'] = __('views/inc/system/systemstatus.accordion_section_ffmpeg_version_required_value');
        $requirements['VERSION']['severity'] = (is_null($command_output) or empty(trim($command_output))) ? SystemStatusSeverity::Warning : SystemStatusSeverity::Success;

        return $requirements;
    }

    /**
     * Tests the SMTP connection based on the `MAIL_` environment settings.
     */
    protected function test_smtp_connection(): string|bool
    {
        if (config('mail.mailers.smtp.verify_peer')) {
            $stream_context = stream_context_create();
        } else {
            $stream_context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
        }

        switch (strtolower(config('mail.mailers.smtp.encryption'))) {
            case 'starttls' || 'tls':
                $connection_uri_scheme = 'tls://';
                break;

            case 'ssl':
                $connection_uri_scheme = 'ssl://';
                break;
        }

        // Example: tls://mail.example.com:587
        $connection_uri = $connection_uri_scheme.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port');

        try {
            $connection = stream_socket_client($connection_uri, $error_code, $error_message, config('mail.mailers.smtp.timeout'), STREAM_CLIENT_CONNECT, $stream_context);
        } catch (ErrorException $error_exception) {
            return "SOCKET #$error_code: ".trim(str_replace('stream_socket_client():', '', $error_exception->getMessage()));
        }

        $res = fgets($connection, 256);
        if (substr($res, 0, 3) !== '220') {
            return "#$error_code: $error_message";
        }

        fwrite($connection, 'HELO '.config('mail.mailers.smtp.local_domain')."\n");
        $res = fgets($connection, 256);
        if (substr($res, 0, 3) !== '250') {
            return "HELO #$error_code: $error_message";
        }

        if (config('mail.mailers.smtp.username') and config('mail.mailers.smtp.password')) {
            fwrite($connection, "AUTH LOGIN\n");
            $res = fgets($connection, 256);
            if (substr($res, 0, 3) !== '334') {
                return "AUTH LOGIN #$error_code: $error_message";
            }

            fwrite($connection, base64_encode(config('mail.mailers.smtp.username'))."\n");
            $res = fgets($connection, 256);
            if (substr($res, 0, 3) !== '334') {
                return "AUTH USERNAME #$error_code: $error_message";
            }

            fwrite($connection, base64_encode(config('mail.mailers.smtp.password'))."\n");
            $res = fgets($connection, 256);
            if (substr($res, 0, 3) !== '235') {
                return "AUTH PASSWORD #$error_code: $error_message";
            }
        }

        fwrite($connection, "QUIT\n");
        $res = fgets($connection, 256);
        if (substr($res, 0, 3) !== '221') {
            return "QUIT #$error_code: $error_message";
        }

        fclose($connection);

        return true;
    }

    /**
     * Checks Mail connection.
     */
    protected function check_mail_connection(): array
    {
        $requirements = [];

        $requirements['TEST']['name'] = __('views/inc/system/systemstatus.accordion_section_mail_connection');
        $requirements['TEST']['required_value'] = __('views/inc/system/systemstatus.accordion_section_mail_connection_required_value');

        switch (config('mail.default')) {
            case 'smtp':
                $smtp_connection_test_result = $this->test_smtp_connection();

                if (is_bool($smtp_connection_test_result)) {
                    $requirements['TEST']['current_value'] = __('views/inc/system/systemstatus.accordion_section_mail_connection_current_value_connected');
                    $requirements['TEST']['severity'] = SystemStatusSeverity::Success;
                } else {
                    $requirements['TEST']['current_value'] = __('views/inc/system/systemstatus.accordion_section_mail_connection_current_value_error', ['exception' => $smtp_connection_test_result]);
                    $requirements['TEST']['severity'] = SystemStatusSeverity::Warning;
                }

                break;
            default:
                $requirements['TEST']['current_value'] = __('views/inc/system/systemstatus.accordion_section_mail_connection_current_value_unsupported_mailer_for_testing');
                $requirements['TEST']['severity'] = SystemStatusSeverity::Warning;
                break;
        }

        return $requirements;
    }

    /**
     * Checks versions.
     */
    protected function check_versions(): array
    {
        $requirements = [];

        $requirements['PHP_VERSION']['name'] = __('views/inc/system/systemstatus.accordion_section_version_php');
        $requirements['PHP_VERSION']['current_value'] = PHP_VERSION;
        $requirements['PHP_VERSION']['severity'] = SystemStatusSeverity::Info;

        $requirements['LARAVEL_VERSION']['name'] = __('views/inc/system/systemstatus.accordion_section_version_laravel');
        $requirements['LARAVEL_VERSION']['current_value'] = Application::VERSION;
        $requirements['LARAVEL_VERSION']['severity'] = SystemStatusSeverity::Info;

        return $requirements;
    }

    /**
     * Checks various information.
     */
    protected function check_various_information(): array
    {
        $requirements = [];

        $requirements['IS_GIT_DEPLOYMENT']['name'] = __('views/inc/system/systemstatus.accordion_section_various_git_deployment');
        $requirements['IS_GIT_DEPLOYMENT']['current_value'] = (file_exists('../.git/')) ? __('views/inc/system/systemstatus.accordion_section_various_git_deployment_current_value_yes') : __('views/inc/system/systemstatus.accordion_section_various_git_deployment_current_value_no');
        $requirements['IS_GIT_DEPLOYMENT']['required_value'] = null;
        $requirements['IS_GIT_DEPLOYMENT']['severity'] = SystemStatusSeverity::Info;

        $requirements['APP_ENVIRONMENT']['name'] = __('views/inc/system/systemstatus.accordion_section_various_app_env');
        $requirements['APP_ENVIRONMENT']['current_value'] = Config::get('app.env');
        $requirements['APP_ENVIRONMENT']['required_value'] = __('views/inc/system/systemstatus.accordion_section_various_app_env_required_value');
        $requirements['APP_ENVIRONMENT']['severity'] = SystemStatusSeverity::Info;

        $requirements['APP_DEBUG']['name'] = __('views/inc/system/systemstatus.accordion_section_various_app_debug');
        $requirements['APP_DEBUG']['current_value'] = (Config::get('app.debug')) ? __('views/inc/system/systemstatus.accordion_section_various_app_debug_current_value_enabled') : __('views/inc/system/systemstatus.accordion_section_various_app_debug_current_value_disabled');
        $requirements['APP_DEBUG']['required_value'] = __('views/inc/system/systemstatus.accordion_section_various_app_debug_required_value');
        $requirements['APP_DEBUG']['severity'] = SystemStatusSeverity::Info;

        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            $requirements['SERVER_SOFTWARE']['name'] = __('views/inc/system/systemstatus.accordion_section_various_server_software');
            $requirements['SERVER_SOFTWARE']['current_value'] = $_SERVER['SERVER_SOFTWARE'];
            $requirements['SERVER_SOFTWARE']['required_value'] = null;
            $requirements['SERVER_SOFTWARE']['severity'] = SystemStatusSeverity::Info;
        }

        $requirements['PHP_BINARY']['name'] = __('views/inc/system/systemstatus.accordion_section_various_php_binary');
        $requirements['PHP_BINARY']['current_value'] = PHP_BINARY;
        $requirements['PHP_BINARY']['required_value'] = null;
        $requirements['PHP_BINARY']['severity'] = SystemStatusSeverity::Info;

        return $requirements;
    }

    /**
     * Returns a summary of the system status in JSON format.
     */
    public function system_status_json($optional_information = true): array
    {
        $system_status = [];

        $system_status['PHP']['VERSION'] = $this->check_php_version();
        $system_status['PHP']['EXTENSIONS'] = $this->check_php_extensions();
        $system_status['PHP']['INI_SETTINGS'] = $this->check_php_ini_settings();
        $system_status['DATABASE']['CONNECTION'] = $this->check_database_connection();
        $system_status['DATABASE']['SETTINGS'] = $this->check_database_settings();
        $system_status['PERMISSIONS']['DIRECTORIES'] = $this->check_directories();
        $system_status['QUEUE']['HEALTH'] = $this->check_queue_health();
        $system_status['REDIS']['CONNECTION'] = $this->check_redis_connection();
        $system_status['FFMPEG']['VERSION'] = $this->check_ffmpeg_version();
        $system_status['MAIL']['CONNECTION'] = $this->check_mail_connection();

        if ($optional_information) {
            $system_status['VERSIONS']['SOFTWARE'] = $this->check_versions();
            $system_status['VARIOUS']['INFORMATION'] = $this->check_various_information();
        }

        return $system_status;
    }

    public function system_status(): array
    {
        $system_status = collect(json_decode(json_encode($this->system_status_json())));
        $php_status = collect($system_status['PHP']);
        $php_extensions = collect($php_status['EXTENSIONS']);
        $php_ini_settings = collect($php_status['INI_SETTINGS']);

        $db_status = collect($system_status['DATABASE']);
        $db_status_connection = collect($db_status['CONNECTION']);
        $db_status_settings = collect($db_status['SETTINGS']);

        $permission_status = collect($system_status['PERMISSIONS']);
        $permission_status_dir = collect($permission_status['DIRECTORIES']);

        $queue_status = collect($system_status['QUEUE']);
        $queue_health_size = collect($queue_status['HEALTH']);

        $redis_staus = collect($system_status['REDIS']);
        $redis_staus_connection = collect($redis_staus['CONNECTION']);

        $ffmpeg_status = collect($system_status['FFMPEG']);
        $ffmpeg_status_version = collect($ffmpeg_status['VERSION']);

        $mail_status = collect($system_status['MAIL']);
        $mail_status_connection = collect($mail_status['CONNECTION']);

        $versions_status = collect($system_status['VERSIONS']);
        $versions_status_software = collect($versions_status['SOFTWARE']);

        $various_status = collect($system_status['VARIOUS']);
        $various_status_information = collect($various_status['INFORMATION']);

        return [
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
            'queue_health_size'=>$queue_health_size,
            'queue_health_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $queue_status),
            'queue_health_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $queue_status),
            'redis_status_connection'=>$redis_staus_connection,
            'redis_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $redis_staus),
            'redis_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $redis_staus),
            'ffmpeg_version'=>$ffmpeg_status_version,
            'ffmpeg_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $ffmpeg_status),
            'ffmpeg_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $ffmpeg_status),
            'mail_status_connection'=>$mail_status_connection,
            'mail_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $mail_status),
            'mail_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $mail_status),
            'version_status_software'=>$versions_status_software,
            'version_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $versions_status),
            'version_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $versions_status),
            'various_status_information'=>$various_status_information,
            'various_warning_count'=>preg_match_all("/\"severity\"\:\"warning\"/", $various_status),
            'various_error_count'=>preg_match_all("/\"severity\"\:\"danger\"/", $various_status),
            'system_status_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $system_status),
            'system_status_danger_count' => preg_match_all("/\"severity\"\:\"danger\"/", $system_status),
        ];
    }
}
