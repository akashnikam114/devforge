<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Restriction;
use App\Helpers\GeneralHelper;

class AppMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        if (GeneralHelper::isAllowedRoute($request, [])) {
            return $next($request);
        }

        $restriction = Restriction::select(['id', 'is_restriction_enabled'])->find(1);

        if ($restriction && $restriction->is_restriction_enabled) {
            [$responseData, $statusCode] = GeneralHelper::getRestrictionData(1);
            return response()->json($responseData, $statusCode);
        }

        return $next($request);
    }
}
