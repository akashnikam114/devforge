<?php

namespace App\Helpers;

use App\Models\BusinessSetting;

class BusinessSettingHelper
{
    public static function getBusinessInfo($key)
    {
        return BusinessSetting::where('key', $key)->pluck('value')->first();
    }

    public static function getAssetUrl($key, $fallback)
    {
        $value = self::getBusinessInfo($key) ?: $fallback;

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        if (str_starts_with($value, 'assets/') || str_starts_with($value, 'pwa/')) {
            return asset($value);
        }

        return asset('storage/' . ltrim($value, '/'));
    }
}
