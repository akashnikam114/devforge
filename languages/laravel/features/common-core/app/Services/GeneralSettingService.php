<?php

namespace App\Services;

use App\Models\GeneralSetting;

class GeneralSettingService
{
    protected $model;

    public function __construct()
    {
        $this->model = new GeneralSetting();
    }

    public function fetch(int $id = 0)
    {
        return $this->model->find($id);
    }

    public function update(int $id, array $data = [])
    {
        $record = $this->model->find($id);

        if (!$record) {
            return false;
        }

        $fieldsToUpdate = [
            'site_title' => $data['site_title'],
            'support_email' => $data['support_email'],
            'support_phone' => $data['support_phone'] ?? null,
            'default_language' => $data['default_language'],
            'date_format' => $data['date_format'],
            'time_format' => $data['time_format'],
            'items_per_page' => $data['items_per_page'],
        ];

        return $record->update($fieldsToUpdate);
    }
}
