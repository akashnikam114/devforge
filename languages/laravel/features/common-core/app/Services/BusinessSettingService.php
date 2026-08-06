<?php

namespace App\Services;

use App\Models\BusinessSetting;

class BusinessSettingService
{
    protected $model;

    public function __construct()
    {
        $this->model = new BusinessSetting();
    }

    public function fetchRecord(array $data = [])
    {
        $keys = [
            'app_name',
            'app_logo',
            'app_email',
            'app_phone',
            'currency_symbol',
            'admin_maintenance_mode',
            'firebase_project_id',
            'firebase_api_key',
            'encryption_key',
            'privacy_policy',
            'terms_and_conditions',
        ];

        return $this->model
            ->whereIn('key', $keys)
            ->orderByRaw("FIELD(`key`, '" . implode("','", $keys) . "')")
            ->get();
    }

    public function update(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (!empty($key) && $value !== null) {
                $this->model->where('key', $key)->update(['value' => $value]);
            }
        }

        return true;
    }
}
