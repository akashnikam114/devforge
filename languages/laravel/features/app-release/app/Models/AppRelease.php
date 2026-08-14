<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'latest_version',
        'is_force_update',
        'release_notes',
    ];
}
