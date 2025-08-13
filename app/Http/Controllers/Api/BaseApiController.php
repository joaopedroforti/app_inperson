<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseApiController extends Controller
{
    protected function checkAccess($company, $module, $method)
    {
        return $company->plan_config['api_access'][$module][$method] ?? false;
    }
}
