<?php

namespace App\Services;

use App\Models\AppRelease;

class AppReleaseService
{
    protected $model;

    public function __construct()
    {
        $this->model = new AppRelease();
    }

    public function store(array $data = [])
    {
        return $this->model->create([
            'platform' => $data['platform'],
            'latest_version' => $data['latest_version'],
            'is_force_update' => $data['is_force_update'],
            'release_notes' => $data['release_notes'] ?? null,
        ]);
    }

    public function fetch(int $id = 0)
    {
        return $this->model->find($id);
    }

    public function fetchRecord(array $data = [])
    {
        return $this->model
            ->select('id', 'platform', 'latest_version', 'is_force_update', 'release_notes')
            ->orderBy('id', 'DESC');
    }

    public function update(int $id, array $data = [])
    {
        $record = $this->model->find($id);

        if (!$record) {
            return false;
        }

        return $record->update([
            'platform' => $data['platform'],
            'latest_version' => $data['latest_version'],
            'is_force_update' => $data['is_force_update'],
            'release_notes' => $data['release_notes'] ?? null,
        ]);
    }
}
