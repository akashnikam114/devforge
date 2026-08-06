<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\BusinessSettingHelper;

class AdminMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        if (filter_var(BusinessSettingHelper::getBusinessInfo('admin_maintenance_mode'), FILTER_VALIDATE_BOOLEAN)) {
            return response()->view('errors.maintenance');
        }

        return $next($request);
    }
}
