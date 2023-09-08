<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\BannerVariableController as HelpersBannerVariableController;
use App\Http\Requests\BannerVariableOverviewRequest;
use App\Models\Banner;
use App\Models\Instance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Predis\Connection\ConnectionException;

class BannerVariableController extends Controller
{
    /**
     * Display the overview page.
     */
    public function getInstanceVariables(int $id)
    {
        $instance = Instance::find($id);

        $redis_connection_error = null;
        $variables_and_values = [];
        try {
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$instance->id.'_datetime'));
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$instance->id.'_servergrouplist'));
            $variables_and_values = array_merge($variables_and_values, Redis::hgetall('instance_'.$instance->id.'_virtualserver_info'));
        } catch (ConnectionException $connection_exception) {
            $redis_connection_error = $connection_exception->getMessage();
            $variables_and_values = [];
        }

        $banner_variable_helper = new HelpersBannerVariableController(null);
        $variables_and_values = array_merge($variables_and_values, $banner_variable_helper->get_client_specific_info_from_cache($instance, Request::ip()));

        return array(
            'redis_connection_error' => $redis_connection_error,
            'variables_and_values' => $variables_and_values,
        );
    }
}
