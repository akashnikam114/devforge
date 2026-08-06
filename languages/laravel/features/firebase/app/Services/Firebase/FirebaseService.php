<?php

namespace App\Services\Firebase;

class FirebaseService
{
    public function isConfigured(): bool
    {
        return filled(config('firebase.project_id')) && filled(config('firebase.credentials'));
    }
}
