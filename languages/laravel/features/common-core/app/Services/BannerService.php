<?php

namespace App\Services;

use App\Models\Banner;

class BannerService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Banner();
    }

    public function store(array $data = [])
    {
        return $this->model->create([
            'image' => $data['image'],
            'link_url' => $data['link_url'] ?? null
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
            'link_url' => $data['link_url']
        ];

        if (isset($data['image'])) {
            $fieldsToUpdate['image'] = $data['image'];
        }

        return $record->update($fieldsToUpdate);
    }
}
