<?php

namespace App\Helpers;

use App\Models\RestrictionSetting;
use Illuminate\Http\Request;

class GeneralHelper
{
    public static function getRestrictionData($restrictionId)
    {
        $data = RestrictionSetting::select(['title', 'image', 'sub_title', 'url_label', 'redirection_url', 'is_button_enabled'])->find($restrictionId);

        $message = 'Service temporarily unavailable. Please try again later.';
        $statusCode = 499;

        $responseData = [
            'status' => false,
            'message' => $message,
            'data' => [
                'title' => $data->title,
                'sub_title' => $data->sub_title,
                'image' => $data->image,
                'url_label' => $data->url_label,
                'redirection_url' => $data->redirection_url,
                'is_button_enabled' => (bool) $data->is_button_enabled,
            ],
        ];

        return [$responseData, $statusCode];
    }

    public static function isAllowedRoute(Request $request, array $additionalRoutes = []): bool
    {
        $defaultAllowedRoutes = [];

        $allowedRoutes = array_merge($defaultAllowedRoutes, $additionalRoutes);
        $path = $request->path();

        foreach ($allowedRoutes as $route) {
            if (stripos($path, $route) !== false) {
                return true;
            }
        }

        return false;
    }
}
