<?php

namespace App\Services;

use App\Models\RestrictionSetting;

class RestrictionSettingService
{
    protected $model;

    public function __construct()
    {
        $this->model = new RestrictionSetting();
    }

    public function fetch(int $id = 0)
    {
        return $this->model->find($id);
    }

    public function fetchRecord(array $data = [])
    {
        return $this->model->orderBy('id', 'DESC');
    }

    public function update(int $id, array $data = [])
    {
        $record = $this->model->find($id);

        if (!$record) {
            return false;
        }

        $fieldsToUpdate = [
            'restriction_name' => $data['restriction_name'],
            'is_restriction_enabled' => $data['is_restriction_enabled'],
            'title' => $data['title'] ?? $record->title,
            'sub_title' => $data['sub_title'] ?? $record->sub_title,
            'image' => $data['image'],
            'url_label' => $data['url_label'] ?? $record->url_label,
            'redirection_url' => $data['redirection_url'] ?? $record->redirection_url,
            'is_button_enabled' => $data['is_button_enabled'],
        ];

        return $record->update($fieldsToUpdate);
    }
}
