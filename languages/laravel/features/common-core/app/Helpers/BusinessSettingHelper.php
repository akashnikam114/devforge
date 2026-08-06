<?php

namespace App\Helpers;

use App\Models\BusinessSetting;

class BusinessSettingHelper
{
    public static function getBusinessInfo($key)
    {
        return BusinessSetting::where('key', $key)->pluck('value')->first();
    }
}
