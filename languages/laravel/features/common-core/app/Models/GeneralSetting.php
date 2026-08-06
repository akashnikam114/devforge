<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_title',
        'support_email',
        'support_phone',
        'default_language',
        'date_format',
        'time_format',
        'items_per_page',
    ];
}
