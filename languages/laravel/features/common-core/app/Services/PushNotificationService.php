<?php

namespace App\Services;

use App\Models\PushNotification;

class PushNotificationService
{
    protected $model;

    public function __construct()
    {
        $this->model = new PushNotification();
    }

    public function store(array $data = [])
    {
        return $this->model->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'image' => $data['image']
        ]);
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
            'title' => $data['title'],
            'description' => $data['description']
        ];

        if (isset($data['image'])) {
            $fieldsToUpdate['image'] = $data['image'];
        }

        return $record->update($fieldsToUpdate);
    }
}
